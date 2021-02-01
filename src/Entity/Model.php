<?php

namespace App\Entity;

use App\Repository\ModelRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModelRepository::class)
 * @ORM\Table(schema="rv")
 */
class Model
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $slideout = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $msrp;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlideout(): ?bool
    {
        return $this->slideout;
    }

    public function setSlideout(bool $slideout): self
    {
        $this->slideout = $slideout;

        return $this;
    }

    public function getMsrp(): ?int
    {
        return $this->msrp;
    }

    public function setMsrp(?int $msrp): self
    {
        $this->msrp = $msrp;

        return $this;
    }

}
