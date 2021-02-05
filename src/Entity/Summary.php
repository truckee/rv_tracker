<?php

namespace App\Entity;

use App\Repository\SummaryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SummaryRepository::class)
 * @ORM\Table(schema="rv")
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
     * @ORM\Column(type="decimal", precision=10, scale=4)
     */
    private $yr_2017 = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=4)
     */
    private $yr_2016 = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=4)
     */
    private $yr_2015 = 0;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=4)
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
    private $added;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="dates", cascade={"persist", "remove"})
     */
    private $files;

    /**
     * @ORM\Column(type="string", length=2, options={"default"=null})
     */
    private $class;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_2018 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_2019 = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $n_2020 = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $yr_2018 = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $yr_2019 = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $yr_2020 = 0;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYr2017(): ?float
    {
        return $this->yr_2017;
    }

    public function setYr2017(float $yr_2017): self
    {
        $this->yr_2017 = $yr_2017;

        return $this;
    }

    public function getYr2016(): ?float
    {
        return $this->yr_2016;
    }

    public function setYr2016(float $yr_2016): self
    {
        $this->yr_2016 = $yr_2016;

        return $this;
    }

    public function getYr2015(): ?float
    {
        return $this->yr_2015;
    }

    public function setYr2015(float $yr_2015): self
    {
        $this->yr_2015 = $yr_2015;

        return $this;
    }

    public function getYr2014(): ?float
    {
        return $this->yr_2014;
    }

    public function setYr2014(float $yr_2014): self
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

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setDates($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
            // set the owning side to null (unless already changed)
            if ($file->getDates() === $this) {
                $file->setDates(null);
            }
        }

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getN2018(): ?int
    {
        return $this->n_2018;
    }

    public function setN2018(int $n_2018): self
    {
        $this->n_2018 = $n_2018;

        return $this;
    }

    public function getN2019(): ?int
    {
        return $this->n_2019;
    }

    public function setN2019(int $n_2019): self
    {
        $this->n_2019 = $n_2019;

        return $this;
    }

    public function getN2020(): ?int
    {
        return $this->n_2020;
    }

    public function setN2020(int $n_2020): self
    {
        $this->n_2020 = $n_2020;

        return $this;
    }

    public function getYr2018(): ?float
    {
        return $this->yr_2018;
    }

    public function setYr2018(float $yr_2018): self
    {
        $this->yr_2018 = $yr_2018;

        return $this;
    }

    public function getYr2019(): ?float
    {
        return $this->yr_2019;
    }

    public function setYr2019(float $yr_2019): self
    {
        $this->yr_2019 = $yr_2019;

        return $this;
    }

    public function getYr2020(): ?float
    {
        return $this->yr_2020;
    }

    public function setYr2020(float $yr_2020): self
    {
        $this->yr_2020 = $yr_2020;

        return $this;
    }

}
