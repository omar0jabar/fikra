<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SecteurRepository")
 */
class Secteur
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    private $secteurId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEn;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Fond", mappedBy="secteurType")
     */
    private $fondsSecteur;

    public function __construct()
    {
        $this->fondsSecteur = new ArrayCollection();
    }

    public function __toString(): ?string
    {
      return (string)$this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecteurId(): ?int
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
    public function getFondsSecteur(): Collection
    {
        return $this->fondsSecteur;
    }

    public function addFondsSecteur(Fond $fondsSecteur): self
    {
        if (!$this->fondsSecteur->contains($fondsSecteur)) {
            $this->fondsSecteur[] = $fondsSecteur;
            $fondsSecteur->addSecteurType($this);
        }

        return $this;
    }

    public function removeFondsSecteur(Fond $fondsSecteur): self
    {
        if ($this->fondsSecteur->contains($fondsSecteur)) {
            $this->fondsSecteur->removeElement($fondsSecteur);
            $fondsSecteur->removeSecteurType($this);
        }

        return $this;
    }
}
