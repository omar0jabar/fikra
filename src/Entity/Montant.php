<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Utils\TranslateObject;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MontantRepository")
 */
class Montant
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
    private $montantMin;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $montantMax;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Fond", mappedBy="min")
     */
    private $fonds;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Fond", mappedBy="max")
     */
    private $fondsMax;

    public function __construct()
    {
        $this->fonds = new ArrayCollection();
        $this->fondsMax = new ArrayCollection();
    }

    public function __toString(): ?string
    {
      return (String)$this->montantMin;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantMin(): ?string
    {
        return $this->montantMin;
    }

    public function setMontantMin(string $montantMin): self
    {
        $this->montantMin = $montantMin;

        return $this;
    }

    public function getMontantMax(): ?string
    {
        return $this->montantMax;
    }

    public function setMontantMax(string $montantMax): self
    {
        $this->montantMax = $montantMax;

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
            $fond->setMin($this);
        }

        return $this;
    }

    public function removeFond(Fond $fond): self
    {
        if ($this->fonds->contains($fond)) {
            $this->fonds->removeElement($fond);
            // set the owning side to null (unless already changed)
            if ($fond->getMin() === $this) {
                $fond->setMin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Fond[]
     */
    public function getFondsMax(): Collection
    {
        return $this->fondsMax;
    }

    public function addFondsMax(Fond $fondsMax): self
    {
        if (!$this->fondsMax->contains($fondsMax)) {
            $this->fondsMax[] = $fondsMax;
            $fondsMax->setMax($this);
        }

        return $this;
    }

    public function removeFondsMax(Fond $fondsMax): self
    {
        if ($this->fondsMax->contains($fondsMax)) {
            $this->fondsMax->removeElement($fondsMax);
            // set the owning side to null (unless already changed)
            if ($fondsMax->getMax() === $this) {
                $fondsMax->setMax(null);
            }
        }

        return $this;
    }
}
