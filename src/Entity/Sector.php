<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SectorRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"labelFr", "labelEn"})
 */
class Sector
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
     * @Assert\Length(
     *    min=6,
     *    max=255
     * )
     * @Groups({"approved_projects_read"})
     */
    private $labelFr;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Project", mappedBy="sectors")
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\ApprovedProject", mappedBy="sectors")
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
    * @throws \Exception
    */
   public function updatedTimestamps(): void
   {
      $dateTimeNow = new \DateTime('now');
      if ($this->getCreatedAt() === null) {
         $this->setCreatedAt($dateTimeNow);
      }
      $slugify = new Slugify();
      $slug = $slugify->slugify($this->getLabelEn());
      $this->setSlug($slug);
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

   public function getSlug(): ?string
   {
      return $this->slug;
   }

   public function setSlug(string $slug): self
   {
      $this->slug = $slug;

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
            $project->addSector($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            $project->removeSector($this);
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
            $approvedProject->addSector($this);
        }

        return $this;
    }

    public function removeApprovedProject(ApprovedProject $approvedProject): self
    {
        if ($this->approvedProjects->contains($approvedProject)) {
            $this->approvedProjects->removeElement($approvedProject);
            $approvedProject->removeSector($this);
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
