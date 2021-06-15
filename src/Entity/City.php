<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @UniqueEntity(fields={"name"})
 */
class City
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="city")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="city")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApprovedCompany", mappedBy="city")
     */
    private $approvedCompanies;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->approvedCompanies = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCity($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getCity() === $this) {
                $user->setCity(null);
            }
        }

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
            $company->setCity($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            // set the owning side to null (unless already changed)
            if ($company->getCity() === $this) {
                $company->setCity(null);
            }
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
            $approvedCompany->setCity($this);
        }

        return $this;
    }

    public function removeApprovedCompany(ApprovedCompany $approvedCompany): self
    {
        if ($this->approvedCompanies->contains($approvedCompany)) {
            $this->approvedCompanies->removeElement($approvedCompany);
            // set the owning side to null (unless already changed)
            if ($approvedCompany->getCity() === $this) {
                $approvedCompany->setCity(null);
            }
        }

        return $this;
    }
}
