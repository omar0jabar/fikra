<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FinancementTypeRepository")
 */
class FinancementType
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Fond", mappedBy="financements")
     */
    private $fondsFinancement;

    public function __construct()
    {
        $this->fondsFinancement = new ArrayCollection();
    }
    public function __toString(): ?string
    {
      return (String)$this->name;
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
    public function getFondsFinancement(): Collection
    {
        return $this->fondsFinancement;
    }

    public function addFondsFinancement(Fond $fondsFinancement): self
    {
        if (!$this->fondsFinancement->contains($fondsFinancement)) {
            $this->fondsFinancement[] = $fondsFinancement;
            $fondsFinancement->addFinancement($this);
        }

        return $this;
    }

    public function removeFondsFinancement(Fond $fondsFinancement): self
    {
        if ($this->fondsFinancement->contains($fondsFinancement)) {
            $this->fondsFinancement->removeElement($fondsFinancement);
            $fondsFinancement->removeFinancement($this);
        }

        return $this;
    }
}
