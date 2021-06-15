<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApprovedProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(
 *     normalizationContext={"groups"="approved_projects_read"},
 *     collectionOperations={"GET"},
 *     itemOperations={"GET"},
 * )
 */
class ApprovedProject
{
   /**
    * @ORM\Id()
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer")
    * @Groups({"approved_projects_read"})
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=255)
    * @Groups({"approved_projects_read"})
    */
   private $language;

   /**
    * @ORM\OneToOne(targetEntity="App\Entity\Project")
    * @ORM\JoinColumn(nullable=false, onDelete="cascade")
    */
   private $project;

   /**
    * @ORM\Column(type="string", length=255)
    * @Groups({"approved_projects_read"})
    */
   private $name;

   /**
    * @ORM\Column(type="text")
    * @Groups({"approved_projects_read"})
    */
   private $description;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Step", inversedBy="approvedProjects")
    * @Groups({"approved_projects_read"})
    */
   private $step;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Earned", inversedBy="approvedProjects")
    * @ORM\JoinColumn(nullable=false)
    * @Groups({"approved_projects_read"})
    */
   private $earned;

   /**
    * @ORM\Column(type="boolean")
    */
   private $startup;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $denomination;

   /**
    * @ORM\Column(type="date", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $creatingDate;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $rc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"approved_projects_read"})
     */
    private $city;

   /**
    * @ORM\ManyToMany(targetEntity="App\Entity\SalesChannels", inversedBy="approvedProjects")
    * @Groups({"approved_projects_read"})
    */
   private $salesChannels;

   /**
    * @ORM\Column(type="boolean", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $otherSalesChannels;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $moreSalesChannels;

   /**
    * @ORM\ManyToMany(targetEntity="App\Entity\Sector", inversedBy="approvedProjects")
    * @Groups({"approved_projects_read"})
    */
   private $sectors;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $moreSectors;

   /**
    * @ORM\ManyToMany(targetEntity="App\Entity\BusinessModel", inversedBy="approvedProjects")
    * @Groups({"approved_projects_read"})
    */
   private $businessModels;

   /**
    * @ORM\Column(type="boolean", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $otherBusinessModel;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $moreBusinessModel;

   /**
    * @ORM\Column(type="boolean", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $morocco;

   /**
    * @ORM\Column(type="boolean", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $otherCountry;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $foreignCountry;

   /**
    * @ORM\Column(type="boolean", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $marketResearch;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\ApprovedAvantage", mappedBy="approvedProject", orphanRemoval=true)
    * @Groups({"approved_projects_read"})
    */
   private $avantages;

   /**
    * @ORM\Column(type="integer", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $budget;

   /**
    * @ORM\Column(type="integer", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $raised;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"approved_projects_read"})
     */
    private $hasNotAmount;

   /**
    * @ORM\Column(type="integer", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $amount;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\ApprovedProjectFinance", mappedBy="approvedProject", orphanRemoval=true)
    * @Groups({"approved_projects_read"})
    */
   private $projectFinances;

   /**
    * @ORM\Column(type="text", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $summary;

   /**
    * @ORM\Column(type="text", nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $express;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\ApprovedTeamMember", mappedBy="approvedProject", orphanRemoval=true)
    * @Groups({"approved_projects_read"})
    */
   private $teamMembers;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\ApprovedDocument", mappedBy="approvedProject", orphanRemoval=true)
    */
   private $documents;

   /**
    * @ORM\OneToMany(targetEntity="App\Entity\ApprovedGalleryPhoto", mappedBy="approvedProject", orphanRemoval=true)
    * @Groups({"approved_projects_read"})
    */
   private $galleryPhotos;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $video;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="approvedProjects")
    * @ORM\JoinColumn(nullable=false)
    * @Groups({"approved_projects_read"})
    */
   private $author;

   /**
    * @ORM\Column(type="string", length=255)
    * @Groups({"approved_projects_read"})
    */
   private $slug;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $imageCoverName;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Groups({"approved_projects_read"})
    */
   private $logoName;

   /**
    * @ORM\Column(type="boolean")
    * @Groups({"approved_projects_read"})
    */
   private $isVerified;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"approved_projects_read"})
     */
    private $isApproved;

   /**
    * @ORM\Column(type="boolean")
    */
   private $isDeleted;

   /**
    * @ORM\Column(type="datetime")
    */
   private $createdAt;

   /**
    * @ORM\Column(type="datetime", nullable=true)
    */
   private $updatedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApprovedService", mappedBy="approvedProject", orphanRemoval=true)
     * @Groups({"approved_projects_read"})
     */
    private $services;
   
   public function __construct()
   {
      $this->services = new ArrayCollection();
      $this->salesChannels = new ArrayCollection();
      $this->sectors = new ArrayCollection();
      $this->businessModels = new ArrayCollection();
      $this->avantages = new ArrayCollection();
      $this->teamMembers = new ArrayCollection();
      $this->documents = new ArrayCollection();
      $this->galleryPhotos = new ArrayCollection();
      $this->projectFinances = new ArrayCollection();
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
         $this->setIsDeleted(0);
      }
      $this->setUpdatedAt($dateTimeNow);
   }

   public function getPourcentAmount() {
       $pourcent = $this->getRaised() * 100 / $this->getBudget();
       return round($pourcent);
   }

    /**
     * @Groups({"approved_projects_read"})
     * @return int
     */
    public function getCountLikes()
    {
        return $this->getProject()->getLikes()->count();
    }

   public function getId(): ?int
   {
      return $this->id;
   }

   public function setId(int $id): self
   {
      $this->id = $id;

      return $this;
   }

   public function getProject(): ?Project
   {
      return $this->project;
   }

   public function setProject(Project $project): self
   {
      $this->project = $project;

      return $this;
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

   public function getDescription(): ?string
   {
      return $this->description;
   }

   public function setDescription(string $description): self
   {
      $this->description = $description;

      return $this;
   }

   public function getStep(): ?Step
   {
      return $this->step;
   }

   public function setStep(?Step $step): self
   {
      $this->step = $step;

      return $this;
   }

   public function getEarned(): ?Earned
   {
      return $this->earned;
   }

   public function setEarned(?Earned $earned): self
   {
      $this->earned = $earned;

      return $this;
   }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

   /**
    * @return Collection|SalesChannels[]
    */
   public function getSalesChannels(): Collection
   {
      return $this->salesChannels;
   }

   public function addSalesChannel(SalesChannels $salesChannel): self
   {
      if (!$this->salesChannels->contains($salesChannel)) {
         $this->salesChannels[] = $salesChannel;
      }

      return $this;
   }

   public function removeSalesChannel(SalesChannels $salesChannel): self
   {
      if ($this->salesChannels->contains($salesChannel)) {
         $this->salesChannels->removeElement($salesChannel);
      }

      return $this;
   }

   public function getOtherSalesChannels(): ?bool
   {
      return $this->otherSalesChannels;
   }

   public function setOtherSalesChannels(?bool $otherSalesChannels): self
   {
      $this->otherSalesChannels = $otherSalesChannels;

      return $this;
   }

   public function getMoreSalesChannels(): ?string
   {
      return $this->moreSalesChannels;
   }

   public function setMoreSalesChannels(?string $moreSalesChannels): self
   {
      $this->moreSalesChannels = $moreSalesChannels;

      return $this;
   }

   /**
    * @return Collection|Sector[]
    */
   public function getSectors(): Collection
   {
      return $this->sectors;
   }

   public function addSector(Sector $sector): self
   {
      if (!$this->sectors->contains($sector)) {
         $this->sectors[] = $sector;
      }

      return $this;
   }

   public function removeSector(Sector $sector): self
   {
      if ($this->sectors->contains($sector)) {
         $this->sectors->removeElement($sector);
      }

      return $this;
   }

   public function getMoreSectors(): ?string
   {
      return $this->moreSectors;
   }

   public function setMoreSectors(?string $moreSectors): self
   {
      $this->moreSectors = $moreSectors;

      return $this;
   }

   /**
    * @return Collection|BusinessModel[]
    */
   public function getBusinessModels(): Collection
   {
      return $this->businessModels;
   }

   public function addBusinessModel(BusinessModel $businessModel): self
   {
      if (!$this->businessModels->contains($businessModel)) {
         $this->businessModels[] = $businessModel;
      }

      return $this;
   }

   public function removeBusinessModel(BusinessModel $businessModel): self
   {
      if ($this->businessModels->contains($businessModel)) {
         $this->businessModels->removeElement($businessModel);
      }

      return $this;
   }

   public function getOtherBusinessModel(): ?bool
   {
      return $this->otherBusinessModel;
   }

   public function setOtherBusinessModel(?bool $otherBusinessModel): self
   {
      $this->otherBusinessModel = $otherBusinessModel;

      return $this;
   }

   public function getMoreBusinessModel(): ?string
   {
      return $this->moreBusinessModel;
   }

   public function setMoreBusinessModel(?string $moreBusinessModel): self
   {
      $this->moreBusinessModel = $moreBusinessModel;

      return $this;
   }

   public function getMorocco(): ?bool
   {
      return $this->morocco;
   }

   public function setMorocco(?bool $morocco): self
   {
      $this->morocco = $morocco;

      return $this;
   }

   public function getOtherCountry(): ?bool
   {
      return $this->otherCountry;
   }

   public function setOtherCountry(?bool $otherCountry): self
   {
      $this->otherCountry = $otherCountry;

      return $this;
   }

   public function getForeignCountry(): ?string
   {
      return $this->foreignCountry;
   }

   public function setForeignCountry(?string $foreignCountry): self
   {
      $this->foreignCountry = $foreignCountry;

      return $this;
   }

   public function getMarketResearch(): ?bool
   {
      return $this->marketResearch;
   }

   public function setMarketResearch(?bool $marketResearch): self
   {
      $this->marketResearch = $marketResearch;

      return $this;
   }

    /**
     * @return Collection|ApprovedService[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(ApprovedService $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setApprovedProject($this);
        }

        return $this;
    }

    public function removeService(ApprovedService $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getApprovedProject() === $this) {
                $service->setApprovedProject(null);
            }
        }

        return $this;
    }

   /**
    * @return Collection|ApprovedAvantage[]
    */
   public function getAvantages(): Collection
   {
      return $this->avantages;
   }

   public function addAvantage(ApprovedAvantage $avantage): self
   {
      if (!$this->avantages->contains($avantage)) {
         $this->avantages[] = $avantage;
         $avantage->setApprovedProject($this);
      }

      return $this;
   }

   public function removeAvantage(ApprovedAvantage $avantage): self
   {
      if ($this->avantages->contains($avantage)) {
         $this->avantages->removeElement($avantage);
         // set the owning side to null (unless already changed)
         if ($avantage->getApprovedProject() === $this) {
            $avantage->setApprovedProject(null);
         }
      }

      return $this;
   }

   public function getBudget(): ?int
   {
      return $this->budget;
   }

   public function setBudget(?int $budget): self
   {
      $this->budget = $budget;

      return $this;
   }

   public function getRaised(): ?int
   {
      return $this->raised;
   }

   public function setRaised(?int $raised): self
   {
      $this->raised = $raised;

      return $this;
   }

   public function getAmount(): ?int
   {
      return $this->amount;
   }

   public function setAmount(?int $amount): self
   {
      $this->amount = $amount;

      return $this;
   }

   public function getSummary(): ?string
   {
      return $this->summary;
   }

   public function setSummary(?string $summary): self
   {
      $this->summary = $summary;

      return $this;
   }

   public function getExpress(): ?string
   {
      return $this->express;
   }

   public function setExpress(?string $express): self
   {
      $this->express = $express;

      return $this;
   }

   /**
    * @return Collection|ApprovedTeamMember[]
    */
   public function getTeamMembers(): Collection
   {
      return $this->teamMembers;
   }

   public function addTeamMember(ApprovedTeamMember $teamMember): self
   {
      if (!$this->teamMembers->contains($teamMember)) {
         $this->teamMembers[] = $teamMember;
         $teamMember->setApprovedProject($this);
      }

      return $this;
   }

   public function removeTeamMember(ApprovedTeamMember $teamMember): self
   {
      if ($this->teamMembers->contains($teamMember)) {
         $this->teamMembers->removeElement($teamMember);
         // set the owning side to null (unless already changed)
         if ($teamMember->getApprovedProject() === $this) {
            $teamMember->setApprovedProject(null);
         }
      }

      return $this;
   }

   public function getDenomination(): ?string
   {
      return $this->denomination;
   }

   public function setDenomination(?string $denomination): self
   {
      $this->denomination = $denomination;

      return $this;
   }

   public function getCreatingDate(): ?\DateTimeInterface
   {
      return $this->creatingDate;
   }

   public function setCreatingDate(?\DateTimeInterface $creatingDate): self
   {
      $this->creatingDate = $creatingDate;

      return $this;
   }

   public function getRc(): ?string
   {
      return $this->rc;
   }

   public function setRc(?string $rc): self
   {
      $this->rc = $rc;

      return $this;
   }

   /**
    * @return Collection|ApprovedDocument[]
    */
   public function getDocuments(): Collection
   {
      return $this->documents;
   }

   public function addDocument(ApprovedDocument $document): self
   {
      if (!$this->documents->contains($document)) {
         $this->documents[] = $document;
         $document->setApprovedProject($this);
      }

      return $this;
   }

   public function removeDocument(ApprovedDocument $document): self
   {
      if ($this->documents->contains($document)) {
         $this->documents->removeElement($document);
         // set the owning side to null (unless already changed)
         if ($document->getApprovedProject() === $this) {
            $document->setApprovedProject(null);
         }
      }

      return $this;
   }

   /**
    * @return Collection|ApprovedGalleryPhoto[]
    */
   public function getGalleryPhotos(): Collection
   {
      return $this->galleryPhotos;
   }

   public function addGalleryPhoto(ApprovedGalleryPhoto $galleryPhoto): self
   {
      if (!$this->galleryPhotos->contains($galleryPhoto)) {
         $this->galleryPhotos[] = $galleryPhoto;
         $galleryPhoto->setApprovedProject($this);
      }

      return $this;
   }

   public function removeGalleryPhoto(ApprovedGalleryPhoto $galleryPhoto): self
   {
      if ($this->galleryPhotos->contains($galleryPhoto)) {
         $this->galleryPhotos->removeElement($galleryPhoto);
         // set the owning side to null (unless already changed)
         if ($galleryPhoto->getApprovedProject() === $this) {
            $galleryPhoto->setApprovedProject(null);
         }
      }

      return $this;
   }

   public function getVideo(): ?string
   {
      return $this->video;
   }

   public function getIdVideo(): ?string
   {
      parse_str( parse_url( $this->video, PHP_URL_QUERY ), $my_array_of_vars );
      return isset($my_array_of_vars['v']) ? $my_array_of_vars['v'] : $this->video;
   }

   public function setVideo(?string $video): self
   {
      $this->video = $video;

      return $this;
   }

   public function getAuthor(): ?User
   {
      return $this->author;
   }

   public function setAuthor(?User $author): self
   {
      $this->author = $author;

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

   public function getImageCoverName(): ?string
   {
      return $this->imageCoverName;
   }

   public function setImageCoverName(?string $imageCoverName): self
   {
      $this->imageCoverName = $imageCoverName;

      return $this;
   }

   public function getLogoName(): ?string
   {
      return $this->logoName;
   }

   public function setLogoName(?string $logoName): self
   {
      $this->logoName = $logoName;

      return $this;
   }

   public function getIsDeleted(): ?bool
   {
      return $this->isDeleted;
   }

   public function setIsDeleted(bool $isDeleted): self
   {
      $this->isDeleted = $isDeleted;

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

   public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
   {
      $this->updatedAt = $updatedAt;

      return $this;
   }

   public function getStartup(): ?bool
   {
       return $this->startup;
   }

   public function setStartup(bool $startup): self
   {
       $this->startup = $startup;

       return $this;
   }

   public function getIsVerified(): ?bool
   {
       return $this->isVerified;
   }

   public function setIsVerified(bool $isVerified): self
   {
       $this->isVerified = $isVerified;

       return $this;
   }

    public function getIsApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

   public function getLanguage(): ?string
   {
       return $this->language;
   }

   public function setLanguage(string $language): self
   {
       $this->language = $language;

       return $this;
   }

   /**
    * @return Collection|ApprovedProjectFinance[]
    */
   public function getProjectFinances(): Collection
   {
       return $this->projectFinances;
   }

   public function addApprovedProjectFinance(ApprovedProjectFinance $approvedProjectFinance): self
   {
       if (!$this->projectFinances->contains($approvedProjectFinance)) {
           $this->projectFinances[] = $approvedProjectFinance;
           $approvedProjectFinance->setApprovedProject($this);
       }

       return $this;
   }

   public function removeApprovedProjectFinance(ApprovedProjectFinance $approvedProjectFinance): self
   {
       if ($this->projectFinances->contains($approvedProjectFinance)) {
           $this->projectFinances->removeElement($approvedProjectFinance);
           // set the owning side to null (unless already changed)
           if ($approvedProjectFinance->getApprovedProject() === $this) {
               $approvedProjectFinance->setApprovedProject(null);
           }
       }

       return $this;
   }

    public function getHasNotAmount(): ?bool
    {
        return $this->hasNotAmount;
    }

    public function setHasNotAmount(?bool $hasNotAmount): self
    {
        $this->hasNotAmount = $hasNotAmount;

        return $this;
    }

    public function getOrderBy(): ?int
    {
        return $this->orderBy;
    }

    public function setOrderBy(?int $order): self
    {
        $this->orderBy = $order;

        return $this;
    }

}
