<?php


namespace App\Scraper;


use App\Scraper\Model\City;
use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

final class CityScraper
{
    public const URI = 'https://turizmehli.com/';


    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return  City[]
     */
    public function getCities(): array
    {
        $Cities = [];


        $this->client->request('GET', self::URI)
            ->filterXPath('//*[@id="main"]/div[2]/div[2]/li/ul/li/a')
            ->each(function ($node) use (&$Cities){
                /**
                 * @var Crawler $node
                 */
                $Cities[] = (new City())
                    ->setName($node->text())
                    ;
            });

        return $Cities;


    }

}
