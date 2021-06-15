<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DomainRepository")
 * @UniqueEntity(fields={"labelFr"})
 * @UniqueEntity(fields={"labelEn"})
 */
class Domain
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $labelFr;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $labelEn;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Company", mappedBy="domain")
     */
    private $companies;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ApprovedCompany", mappedBy="domain")
     */
    private $approvedCompanies;

    public function __construct()
    {
        $this->companies = new ArrayCollection();
        $this->approvedCompanies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
    {
        return (string)$this->labelFr;
    }

    public function getLabelFr(): ?string
    {
        return $this->labelFr;
    }

    public function setLabelFr(string $label): self
    {
        $this->labelFr = $label;

        return $this;
    }

    public function getLabelEn(): ?string
    {
        return $this->labelEn;
    }

    public function setLabelEn(string $label): self
    {
        $this->labelEn = $label;

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->addDomain($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            $company->removeDomain($this);
        }

        return $this;
    }

    /**
     * @return Collection|ApprovedCompany[]
     */
    public function getApprovedCompanies(): Collection
    {
        return $this->approvedCompanies;
    }

    public function addApprovedCompany(ApprovedCompany $approvedCompany): self
    {
        if (!$this->approvedCompanies->contains($approvedCompany)) {
            $this->approvedCompanies[] = $approvedCompany;
            $approvedCompany->addDomain($this);
        }

        return $this;
    }

    public function removeApprovedCompany(ApprovedCompany $approvedCompany): self
    {
        if ($this->approvedCompanies->contains($approvedCompany)) {
            $this->approvedCompanies->removeElement($approvedCompany);
            $approvedCompany->removeDomain($this);
        }

        return $this;
    }
}
