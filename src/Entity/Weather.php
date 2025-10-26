<?php

namespace App\Entity;

use App\Repository\WeatherRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeatherRepository::class)]
class Weather
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'weathers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $celcius = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $windSpeed = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $precipitation = null;

    #[ORM\Column(type: Types::FLOAT)]
    private ?float $humidity = null;

    // gettery i settery...
    public function getId(): ?int { return $this->id; }

    public function getLocation(): ?Location { return $this->location; }
    public function setLocation(?Location $location): self { $this->location = $location; return $this; }

    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): self { $this->date = $date; return $this; }

    public function getCelcius(): ?float { return $this->celcius; }
    public function setCelcius(float $celcius): self { $this->celcius = $celcius; return $this; }

    public function getWindSpeed(): ?float { return $this->windSpeed; }
    public function setWindSpeed(float $windSpeed): self { $this->windSpeed = $windSpeed; return $this; }

    public function getPrecipitation(): ?float { return $this->precipitation; }
    public function setPrecipitation(float $precipitation): self { $this->precipitation = $precipitation; return $this; }

    public function getHumidity(): ?float { return $this->humidity; }
    public function setHumidity(float $humidity): self { $this->humidity = $humidity; return $this; }
}
