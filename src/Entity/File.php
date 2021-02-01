<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File
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
    private $filename;
//
//    /**
//     * @ORM\Column(type="date")
//     */
//    private $added;

    /**
     * @ORM\OneToMany(targetEntity=RV::class, mappedBy="file", cascade={"persist", "remove"})
     */
    private $rVs;

    /**
     * @ORM\ManyToOne(targetEntity=Summary::class, inversedBy="files", cascade={"persist", "remove"})
     */
    private $dates;

    public function __construct()
    {
        $this->rVs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

//
//    public function getAdded(): ?\DateTimeInterface
//    {
//        return $this->added;
//    }
//
//    public function setAdded(\DateTimeInterface $added): self
//    {
//        $this->added = $added;
//
//        return $this;
//    }

    /**
     * @return Collection|RV[]
     */
    public function getRVs(): Collection
    {
        return $this->rVs;
    }

    public function addRV(RV $rV): self
    {
        if (!$this->rVs->contains($rV)) {
            $this->rVs[] = $rV;
            $rV->setFile($this);
        }

        return $this;
    }

    public function removeRV(RV $rV): self
    {
        if ($this->rVs->contains($rV)) {
            $this->rVs->removeElement($rV);
            // set the owning side to null (unless already changed)
            if ($rV->getFile() === $this) {
                $rV->setFile(null);
            }
        }

        return $this;
    }

    public function getDates(): ?Summary
    {
        return $this->dates;
    }

    public function setDates(?Summary $dates): self
    {
        $this->dates = $dates;

        return $this;
    }

}
