<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BusinessModelRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"labelFr", "labelEn"})
 */
class BusinessModel
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
    private $labelFr;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="businessModels")
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ApprovedProject", mappedBy="businessModels")
     */
    private $approvedProjects;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"approved_projects_read"})
     */
    private $labelEn;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->approvedProjects = new ArrayCollection();
    }

   /**
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    */
   public function updatedTimestamps(): void
   {
      $dateTimeNow = new \DateTime('now');
      if ($this->getCreatedAt() === null) {
         $this->setCreatedAt($dateTimeNow);
      }
      $this->setUpdatedAt($dateTimeNow);
   }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabelFr(): ?string
    {
        return $this->labelFr;
    }

   public function __toString(): ?string
   {
      return (string)$this->labelFr;
   }

    public function setLabelFr(string $labelFr): self
    {
        $this->labelFr = $labelFr;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setBusinessModel($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getBusinessModel() === $this) {
                $project->setBusinessModel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ApprovedProject[]
     */
    public function getApprovedProjects(): Collection
    {
        return $this->approvedProjects;
    }

    public function addApprovedProject(ApprovedProject $approvedProject): self
    {
        if (!$this->approvedProjects->contains($approvedProject)) {
            $this->approvedProjects[] = $approvedProject;
            $approvedProject->setBusinessModel($this);
        }

        return $this;
    }

    public function removeApprovedProject(ApprovedProject $approvedProject): self
    {
        if ($this->approvedProjects->contains($approvedProject)) {
            $this->approvedProjects->removeElement($approvedProject);
            // set the owning side to null (unless already changed)
            if ($approvedProject->getBusinessModel() === $this) {
                $approvedProject->setBusinessModel(null);
            }
        }

        return $this;
    }

    public function getLabelEn(): ?string
    {
        return $this->labelEn;
    }

    public function setLabelEn(?string $labelEn): self
    {
        $this->labelEn = $labelEn;

        return $this;
    }
}
