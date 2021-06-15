<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApprovedUseFundRepository")
 */
class ApprovedUseFund
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
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ApprovedCompany", inversedBy="useOfFundsCollected", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $approvedCompany;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
    {
        return (string)$this->description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getApprovedCompany(): ?ApprovedCompany
    {
        return $this->approvedCompany;
    }

    public function setApprovedCompany(?ApprovedCompany $approvedCompany): self
    {
        $this->approvedCompany = $approvedCompany;

        return $this;
    }
}
