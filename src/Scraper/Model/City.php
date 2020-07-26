<?php


namespace App\Scraper\Model;


final class City
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): City
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): City
    {
        $this->name = $name;
        return $this;

    }
}
