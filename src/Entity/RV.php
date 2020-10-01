<?php

namespace App\Entity;

use App\Repository\RVRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RVRepository::class)
 */
class RV
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ymm;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ad_make;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ad_model;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ad_price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ad_location;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ad_year;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYmm(): ?string
    {
        return $this->ymm;
    }

    public function setYmm(string $ymm): self
    {
        $this->ymm = $ymm;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getAdMake(): ?string
    {
        return $this->ad_make;
    }

    public function setAdMake(string $ad_make): self
    {
        $this->ad_make = $ad_make;

        return $this;
    }

    public function getAdModel(): ?string
    {
        return $this->ad_model;
    }

    public function setAdModel(string $ad_model): self
    {
        $this->ad_model = $ad_model;

        return $this;
    }

    public function getAdPrice(): ?string
    {
        return $this->ad_price;
    }

    public function setAdPrice(string $ad_price): self
    {
        $this->ad_price = $ad_price;

        return $this;
    }

    public function getAdLocation(): ?string
    {
        return $this->ad_location;
    }

    public function setAdLocation(string $ad_location): self
    {
        $this->ad_location = $ad_location;

        return $this;
    }

    public function getAdYear(): ?string
    {
        return $this->ad_year;
    }

    public function setAdYear(string $ad_year): self
    {
        $this->ad_year = $ad_year;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }
}
