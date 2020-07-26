<?php


namespace App\Service;


use App\Export\ExcelExport;
use Doctrine\ORM\EntityManagerInterface;

final class ExportService
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getExcelExport(): ExcelExport
    {
        return new ExcelExport($this->entityManager);
    }
}
