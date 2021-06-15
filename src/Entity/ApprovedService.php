<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApprovedServiceRepository")
 */
class ApprovedService
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ApprovedProject", inversedBy="services")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $approvedProject;

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

    public function getApprovedProject(): ?ApprovedProject
    {
        return $this->approvedProject;
    }

    public function setApprovedProject(?ApprovedProject $project): self
    {
        $this->approvedProject = $project;

        return $this;
    }
}
