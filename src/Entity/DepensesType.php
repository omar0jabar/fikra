<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DepensesTypeRepository")
 */
class DepensesType
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
     * @ORM\OneToMany(targetEntity="App\Entity\Fond", mappedBy="depensesType")
     */
    private $fonds;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEn;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Fond", mappedBy="depenses")
     */
    private $depenseFonds;

    public function __construct()
    {
        $this->fonds = new ArrayCollection();
        $this->depenseFonds = new ArrayCollection();
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
            $fond->setDepensesType($this);
        }

        return $this;
    }

    public function removeFond(Fond $fond): self
    {
        if ($this->fonds->contains($fond)) {
            $this->fonds->removeElement($fond);
            // set the owning side to null (unless already changed)
            if ($fond->getDepensesType() === $this) {
                $fond->setDepensesType(null);
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

    /**
     * @return Collection|Fond[]
     */
    public function getDepenseFonds(): Collection
    {
        return $this->depenseFonds;
    }

    public function addDepenseFond(Fond $depenseFond): self
    {
        if (!$this->depenseFonds->contains($depenseFond)) {
            $this->depenseFonds[] = $depenseFond;
            $depenseFond->addDepense($this);
        }

        return $this;
    }

    public function removeDepenseFond(Fond $depenseFond): self
    {
        if ($this->depenseFonds->contains($depenseFond)) {
            $this->depenseFonds->removeElement($depenseFond);
            $depenseFond->removeDepense($this);
        }

        return $this;
    }
}
