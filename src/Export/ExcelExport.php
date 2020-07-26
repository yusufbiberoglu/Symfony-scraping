<?php


namespace App\Export;


use App\Entity\City;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet;
use Symfony\Component\Console\Exception\RuntimeException;

final class ExcelExport
{

    private $entityManager;

    private $spreadsheet;

    private $worksheet;

    private $rowCursor = 1;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->spreadsheet = new Spreadsheet();

        $this->worksheet = $this->spreadsheet->getActiveSheet();

        $this->spreadsheet->getProperties()
            ->setTitle('Cities of Turkey')
            ->setCreator('Yusuf BİBEROĞLU')
            ;

        // Set worksheet title
        $this->worksheet->setTitle('City List');

    }

    private function customizeExcel()
    {
        // Set header font style bold
        $this->worksheet->getStyle('A1:L1')
            ->getFont()
            ->setBold(true);

        // Set font size 14px for all cells
        $this->worksheet->getStyle('A1:'.$this->worksheet->getHighestColumn().$this->worksheet->getHighestRow())
            ->applyFromArray([
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center'
                ],
                'font' => [
                    'size' => 14
                ]
            ]);


        $this->worksheet->getStyle('D2:D'.$this->worksheet->getHighestRow())
            ->getNumberFormat()
            ->setFormatCode(PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        $this->worksheet->getStyle('E2:E'.$this->worksheet->getHighestRow())
            ->getNumberFormat()
            ->setFormatCode(PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Auto update each generated columns width
        foreach ($this->worksheet->getColumnIterator() as $column) {
            $this->worksheet->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(true);
        }

        // Arrange header row's height
        $this->worksheet->getRowDimension(1)->setRowHeight(15);

        // Arrange rows height
        if ($this->worksheet->getHighestRow() > 2) {
            foreach ($this->worksheet->getRowIterator(2) as $row) {
                $this->worksheet->getRowDimension($row->getRowIndex())->setRowHeight(30);
            }
        }
    }

    private function getData(): array
    {
        /**
         * @var $univercities City[]
         */
        $list = [];
        $univercities = $this->entityManager->getRepository(City::class)->findAll();


        foreach ($univercities as $unv) {
            $list[] = [
                $unv->getName(),
            ];
        }
        return $list;
    }

    private function writeHeader(array $header): void
    {
        $column = 1;
        $dimensional = false;

        // Loop over header array and set cell values
        foreach ($header as $key => $value) {

            // If value is string, simply set column value by row and column id
            if (true === is_string($value)) {
                $this->worksheet->setCellValueByColumnAndRow($column, $this->rowCursor, $value);
            }
            // If value is array, set current cell value then loop array and parse second row
            elseif (true === is_array($value)) {
                $this->worksheet->setCellValueByColumnAndRow($column, $this->rowCursor, $key);

                foreach ($value as $index => $content) {
                    $this->worksheet->setCellValueByColumnAndRow($column + $index, $this->rowCursor + 1, $content);
                    $dimensional = true;
                }
            }

            $column++;
        }

        // Increase row cursor after header write
        ++$this->rowCursor;

        // If header is dimensional, increase row counter by one more
        if (true === $dimensional) {
            ++$this->rowCursor;
        }
    }

    private function writeData(array $data): void
    {
        $this->worksheet->fromArray($data, null, 'A'.$this->rowCursor, true);
    }

    public function export(\SplFileInfo $file): \SplFileInfo
    {
        if (false === $file->isFile()) {
            throw new RuntimeException('Given export file not found.');
        }

        // Write header data to spreadsheet
        $this->writeHeader([
            'Üniversite Adı',
        ]);

        // Write content data to spreadsheet
        $this->writeData($this->getData());

        $this->customizeExcel();

        // Save excel content given file
        try {
            (new PhpSpreadsheet\Writer\Xlsx($this->spreadsheet))->save($file->getRealPath());
        } catch (\Exception $e) {
            throw new RuntimeException($e->getMessage());
        }

        return $file;
    }

}
