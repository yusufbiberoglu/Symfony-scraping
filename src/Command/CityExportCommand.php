<?php

namespace App\Command;


use App\Service\ExportService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CityExportCommand extends Command
{
    protected static $defaultName = 'city:export';

    private $exportService;


    public function __construct(ExportService $exportService, string $name = null)
    {
        parent::__construct($name);

        $this->exportService = $exportService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Export City List')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = uniqid('city-export-', false).'.xlsx';

        $progressBar = new ProgressBar($output);
        $progressBar->setFormat('%message% %elapsed:6s% %memory:6s%');
        $progressBar->setMessage("Export file is preparing to {$fileName}...");
        $progressBar->start();

        $export = $this->exportService
            ->getExcelExport()
            ->export(new \SplFileInfo(tempnam(sys_get_temp_dir(), 'city-export')))
            ;

        rename($export->getRealPath(), __DIR__.'/../../'.$fileName);

        $progressBar->finish();
        $progressBar->clear();
        $io->success("{$fileName} export created.");

        return 0;

    }
}






















