<?php

namespace App\Entity;

use App\Repository\SummaryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SummaryRepository::class)
 */
class Summary
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $yr_2017 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $yr_2016 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $yr_2015 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $yr_2014 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_2017 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_2016 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_2015 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_2014 = 0;

    /**
     * @ORM\Column(type="date")
     */
    private $summary_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYr2017(): ?int
    {
        return $this->yr_2017;
    }

    public function setYr2017(int $yr_2017): self
    {
        $this->yr_2017 = $yr_2017;

        return $this;
    }

    public function getYr2016(): ?int
    {
        return $this->yr_2016;
    }

    public function setYr2016(int $yr_2016): self
    {
        $this->yr_2016 = $yr_2016;

        return $this;
    }

    public function getYr2015(): ?int
    {
        return $this->yr_2015;
    }

    public function setYr2015(int $yr_2015): self
    {
        $this->yr_2015 = $yr_2015;

        return $this;
    }

    public function getYr2014(): ?int
    {
        return $this->yr_2014;
    }

    public function setYr2014(int $yr_2014): self
    {
        $this->yr_2014 = $yr_2014;

        return $this;
    }

    public function getN2017(): ?int
    {
        return $this->n_2017;
    }

    public function setN2017(int $n_2017): self
    {
        $this->n_2017 = $n_2017;

        return $this;
    }

    public function getN2016(): ?int
    {
        return $this->n_2016;
    }

    public function setN2016(int $n_2016): self
    {
        $this->n_2016 = $n_2016;

        return $this;
    }

    public function getN2015(): ?int
    {
        return $this->n_2015;
    }

    public function setN2015(int $n_2015): self
    {
        $this->n_2015 = $n_2015;

        return $this;
    }

    public function getN2014(): ?int
    {
        return $this->n_2014;
    }

    public function setN2014(int $n_2014): self
    {
        $this->n_2014 = $n_2014;

        return $this;
    }

    public function getSummaryDate(): ?\DateTimeInterface
    {
        return $this->summary_date;
    }

    public function setSummaryDate(\DateTimeInterface $summary_date): self
    {
        $this->summary_date = $summary_date;

        return $this;
    }
}
