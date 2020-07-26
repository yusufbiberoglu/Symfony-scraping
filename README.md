# symfony-scraping-and-export

This is a example for scraping datas and export with phpspreadsheet.

After download poroject run following commands;

composer install

php bin/console doctrine:database:create

php bin/console doctrine:schema:update --force

php bin/console app:import:city

php bin/console city:export
