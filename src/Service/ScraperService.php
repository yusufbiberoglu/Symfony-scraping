<?php

namespace App\Service;

use App\Scraper\CityScraper;
use Goutte\Client;
use GuzzleHttp\Client as GuzzleClient;

final class ScraperService
{
    private $client;

    public function __construct(GuzzleClient $guzzleClient)
    {
        $client = new Client();
        $client->setClient($guzzleClient);

        // Update browser-like headers
        $client->setHeader('accept','text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8');
        $client->setHeader('accept-encoding','gzip, deflate');
        $client->setHeader('accept-language','en,tr');
        $client->setHeader('cache-control','no-cache');
        $client->setHeader('pragma','no-cache');
        $client->setHeader('dnt','1');
        $client->setHeader('upgrade-insecure-requests','1');
        $client->setHeader('user-agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_13_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36');

        $this->client = $client;

    }

    public function getCityScraper(): CityScraper
    {
        return new CityScraper($this->client);
    }
}
