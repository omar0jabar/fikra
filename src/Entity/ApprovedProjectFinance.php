<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApprovedProjectFinanceRepository")
 */
class ApprovedProjectFinance
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"approved_projects_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"approved_projects_read"})
     */
    private $detail;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ApprovedProject", inversedBy="projectFinances")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $approvedProject;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): self
    {
        $this->detail = $detail;

        return $this;
    }

    public function getApprovedProject(): ?ApprovedProject
    {
        return $this->approvedProject;
    }

    public function setApprovedProject(?ApprovedProject $approvedProject): self
    {
        $this->approvedProject = $approvedProject;

        return $this;
    }
}
