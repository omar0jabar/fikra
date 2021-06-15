<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EligibiliteCritereRepository")
 */
class EligibiliteCritere
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
     * @ORM\OneToMany(targetEntity="App\Entity\Fond", mappedBy="eligibiliteCritere")
     */
    private $fonds;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEn;

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
            $fond->setEligibiliteCritere($this);
        }

        return $this;
    }

    public function removeFond(Fond $fond): self
    {
        if ($this->fonds->contains($fond)) {
            $this->fonds->removeElement($fond);
            // set the owning side to null (unless already changed)
            if ($fond->getEligibiliteCritere() === $this) {
                $fond->setEligibiliteCritere(null);
            }
        }

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
}
