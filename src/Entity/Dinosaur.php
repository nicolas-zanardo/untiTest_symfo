<?php

namespace App\Entity;

use App\Enum\HealthyStatus;

class Dinosaur
{
    private string $name;
    private string $genus;
    private int $length;
    private string $enclosure;
    private HealthyStatus $health = HealthyStatus::HEALTHY;

    public function __construct(string $name, string $genus = 'Unknown', int $length = 0, string $enclosure = 'Unknown')
    {
        $this->name = $name;
        $this->genus = $genus;
        $this->length = $length;
        $this->enclosure = $enclosure;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGenus(): string
    {
        return $this->genus;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getEnclosure(): string
    {
        return $this->enclosure;
    }

    public function getSizeDescription(): string {
        $response = 'Medium';
        if($this->length >= 10) {
            $response = 'Large';
        }
        if($this->length < 5) {
            $response = 'Small';
        }
        return $response;
    }

    public function isAcceptingVisitors() :bool {
        return $this->health !== HealthyStatus::SICK;
    }

    public function setHealth(HealthyStatus $health): void  {
        $this->health = $health;
    }
}
