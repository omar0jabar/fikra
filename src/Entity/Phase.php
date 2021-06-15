<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhaseRepository")
 */
class Phase
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
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEn;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Fond", mappedBy="fondPhases")
     */
    private $fonds;

    public function __construct()
    {
        $this->fonds = new ArrayCollection();
    }

    public function __toString(): ?string
    {
      return (string)$this->name;
    }

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

    public function getNameEn(): ?string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): self
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    /**
     * @return Collection|Fond[]
     */
    public function getFonds(): Collection
    {
        return $this->fonds;
    }

    public function addFond(Fond $fond): self
    {
        if (!$this->fonds->contains($fond)) {
            $this->fonds[] = $fond;
            $fond->addFondPhase($this);
        }

        return $this;
    }

    public function removeFond(Fond $fond): self
    {
        if ($this->fonds->contains($fond)) {
            $this->fonds->removeElement($fond);
            $fond->removeFondPhase($this);
        }

        return $this;
    }
}
