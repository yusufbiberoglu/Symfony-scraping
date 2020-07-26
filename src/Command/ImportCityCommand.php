<?php

namespace App\Command;

use App\Entity\City;
use App\Service\ScraperService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportCityCommand extends Command
{
    protected static $defaultName = 'app:import:city';

    private $scraper;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ScraperService $scraper)
    {
        parent::__construct();

        $this->scraper = $scraper;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import City List')
        ;
    }
    private function truncate(string $entityFQCN): void
    {
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();

        // Resolve table name from given entity FQCN
        $tableName = $this->entityManager
            ->getClassMetadata($entityFQCN)
            ->getTableName();

        // Execute platform truncate query
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeQuery($platform->getTruncateTableSQL($tableName));
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $cities = $this->scraper->getCityScraper()->getCities();


        if ($cities) {
            // Show warning before start insert
            $io->warning('Existent cities will be removed.');

            // Ask user confirmation before update system
            if (false === $input->getOption('no-interaction')) {
                if (false === $io->confirm('Do you want to continue?', false)) {
                    $io->comment('Command terminated.');
                    return -1;
                }
            }

            $this->truncate(City::class);

            foreach($cities as $city) {
                $this->entityManager->persist(
                    (new City())
                        ->setName($city->getName())
                );
            }

            $this->entityManager->flush();

            $io->success(count($cities).' Cities added.');
        } else {
            $io->comment('Cities not found.');
        }

        return 0;
    }
}
