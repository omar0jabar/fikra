<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"name"})
 * @Vich\Uploadable
 */
class Project
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Expression(
     *     "this.getLanguage() in ['en', 'fr']",
     * )
     */
    private $language;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *    max=100
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     * @Assert\Length(
     *    max="256"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(
     *    max=10000,
     * )
     */
    private $summary;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Step", inversedBy="projects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $step;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Earned", inversedBy="projects")
     */
    private $earned;

    /**
     * @ORM\Column(type="boolean")
     */
    private $startup;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *    max=100
     * )
     */
    private $denomination;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Date()
     */
    private $creatingDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex(
     *     pattern     = "/^[0-9]+$/i"
     * )
     * @Assert\Length(
     *    max=6
     * )
     */
    private $rc;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *    max=50
     * )
     */
    private $city;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\SalesChannels", inversedBy="projects")
     */
    private $salesChannels;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $otherSalesChannels;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *    max=100
     * )
     */
    private $moreSalesChannels;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sector", inversedBy="projects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sectors;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *    max=100
     * )
     */
    private $moreSectors;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\BusinessModel", inversedBy="projects")
     * @ORM\JoinColumn(nullable=true)
     */
    private $businessModels;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $otherBusinessModel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(
     *    max=100
     * )
     */
    private $moreBusinessModel;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $morocco;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $otherCountry;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foreignCountry;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $marketResearch;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Avantage", mappedBy="project", orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid(groups={"step2"})
     */
    private $avantages;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *    max="1000000000",
     *    groups={"step2"}
     * )
     */
    private $budget;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *    max="1000000000",
     *    groups={"step2"}
     * )
     */
    private $raised;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Range(
     *    max="1000000000",
     *    groups={"step2"}
     * )
     */
    private $amount;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectFinance", mappedBy="project",
     *    orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     * @Assert\Valid(groups={"step2"})
     */
    private $projectFinances;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *    max=10000,
     *    groups={"step2"}
     * )
     */
    private $express;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TeamMember", mappedBy="project", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $teamMembers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="project", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GalleryPhoto", mappedBy="project", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $galleryPhotos;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url(
     *    groups={"video"}
     *    )
     */
    private $video;

    /**
     * @ORM\Column(type="integer")
     */
    private $stepCreating;

    /**
     * @ORM\Column(type="integer")
     */
    private $stepUpdating;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDraft;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isApproved;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRejected;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isUpdated;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLocked;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageCoverName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="gallery_photo", fileNameProperty="imageCoverName")
     * @Assert\Image(
     *    mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="5M"
     * )
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="project_logo", fileNameProperty="logoName")
     * @Assert\Image(
     *    mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="3M"
     * )
     */
    private $logoFile;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Faq", mappedBy="project")
     */
    private $faqs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prints;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="project")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MailSended", mappedBy="project")
     */
    private $mailSendeds;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RequestDocumentation", mappedBy="project")
     */
    private $requestDocumentations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectLike", mappedBy="project", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasNotAmount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $orderBy;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Service", mappedBy="project", orphanRemoval=true, cascade={"persist"})
     */
    private $services;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaTitle;


    public function __construct()
    {
        $this->salesChannels = new ArrayCollection();
        $this->sectors = new ArrayCollection();
        $this->businessModels = new ArrayCollection();
        $this->avantages = new ArrayCollection();
        $this->teamMembers = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->galleryPhotos = new ArrayCollection();
        $this->projectFinances = new ArrayCollection();
        $this->faqs = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->mailSendeds = new ArrayCollection();
        $this->requestDocumentations = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->services = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     */
    public function initializeProject(): void
    {
        $this->setIsApproved(false);
        $this->setIsRejected(false);
        $this->setIsUpdated(false);
        $this->setIsDeleted(false);
        $this->setStepUpdating(1);
        $this->setIsVerified(false);
        $this->setIsLocked(false);
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @throws \Exception
     */
    public function updatedTimestamps(): void
    {
        $slugify = new Slugify();
        $this->setSlug($slugify->slugify($this->name));

        $dateTimeNow = new \DateTime('now');
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
        $this->setUpdatedAt($dateTimeNow);
    }

    public function getPourcentAmount() {
        $pourcent = $this->getRaised() * 100 / $this->getBudget();
        return round($pourcent);
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

    public function __toString(): ?string
    {
        return (string)$this->name;
    }

    public function getSlug(): ?string
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->name);
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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;

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

    public function setEarned(Earned $earned): self
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

    public function setMorocco(bool $morocco): self
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

    public function setMarketResearch(bool $marketResearch): self
    {
        $this->marketResearch = $marketResearch;

        return $this;
    }

    /**
     * @return Collection|Avantage[]
     */
    public function getAvantages(): Collection
    {
        return $this->avantages;
    }

    public function addAvantage(Avantage $avantage): self
    {
        if (!$this->avantages->contains($avantage)) {
            $this->avantages[] = $avantage;
            $avantage->setProject($this);
        }

        return $this;
    }

    public function removeAvantage(Avantage $avantage): self
    {
        if ($this->avantages->contains($avantage)) {
            $this->avantages->removeElement($avantage);
            // set the owning side to null (unless already changed)
            if ($avantage->getProject() === $this) {
                $avantage->setProject(null);
            }
        }

        return $this;
    }

    public function getBudget(): ?int
    {
        return $this->budget;
    }

    public function setBudget(int $budget): self
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
     * @return Collection|TeamMember[]
     */
    public function getTeamMembers(): Collection
    {
        return $this->teamMembers;
    }

    public function addTeamMember(TeamMember $teamMember): self
    {
        if (!$this->teamMembers->contains($teamMember)) {
            $this->teamMembers[] = $teamMember;
            $teamMember->setProject($this);
        }

        return $this;
    }

    public function removeTeamMember(TeamMember $teamMember): self
    {
        if ($this->teamMembers->contains($teamMember)) {
            $this->teamMembers->removeElement($teamMember);
            // set the owning side to null (unless already changed)
            if ($teamMember->getProject() === $this) {
                $teamMember->setProject(null);
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
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setProject($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getProject() === $this) {
                $document->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GalleryPhoto[]
     */
    public function getGalleryPhotos(): Collection
    {
        return $this->galleryPhotos;
    }

    public function addGalleryPhoto(GalleryPhoto $galleryPhoto): self
    {
        if (!$this->galleryPhotos->contains($galleryPhoto)) {
            $this->galleryPhotos[] = $galleryPhoto;
            $galleryPhoto->setProject($this);
        }

        return $this;
    }

    public function removeGalleryPhoto(GalleryPhoto $galleryPhoto): self
    {
        if ($this->galleryPhotos->contains($galleryPhoto)) {
            $this->galleryPhotos->removeElement($galleryPhoto);
            // set the owning side to null (unless already changed)
            if ($galleryPhoto->getProject() === $this) {
                $galleryPhoto->setProject(null);
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

    public function getStepCreating(): ?int
    {
        return $this->stepCreating;
    }

    public function setStepCreating(int $stepCreating): self
    {
        $this->stepCreating = $stepCreating;

        return $this;
    }

    public function getIsDraft(): ?bool
    {
        return $this->isDraft;
    }

    public function setIsDraft(bool $isDraft): self
    {
        $this->isDraft = $isDraft;

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

    public function getIsRejected(): ?bool
    {
        return $this->isRejected;
    }

    public function setIsRejected(bool $isRejected): self
    {
        $this->isRejected = $isRejected;

        return $this;
    }

    public function getIsUpdated(): ?bool
    {
        return $this->isUpdated;
    }

    public function setIsUpdated(bool $isUpdated): self
    {
        $this->isUpdated = $isUpdated;

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

    public function getStepUpdating(): ?int
    {
        return $this->stepUpdating;
    }

    public function setStepUpdating(int $stepUpdating): self
    {
        $this->stepUpdating = $stepUpdating;

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

    /**
     * @return  File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Project
     * @throws \Exception
     */
    public function setImageFile($imageFile): self
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
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

    /**
     * @return  File|null
     */
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /**
     * @param File|null $logoFile
     * @return Project
     * @throws \Exception
     */
    public function setLogoFile($logoFile): self
    {
        $this->logoFile = $logoFile;
        if ($this->logoFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
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
     * @return Collection|ProjectFinance[]
     */
    public function getProjectFinances(): Collection
    {
        return $this->projectFinances;
    }

    public function addProjectFinance(ProjectFinance $projectFinance): self
    {
        if (!$this->projectFinances->contains($projectFinance)) {
            $this->projectFinances[] = $projectFinance;
            $projectFinance->setProject($this);
        }

        return $this;
    }

    public function removeProjectFinance(ProjectFinance $projectFinance): self
    {
        if ($this->projectFinances->contains($projectFinance)) {
            $this->projectFinances->removeElement($projectFinance);
            // set the owning side to null (unless already changed)
            if ($projectFinance->getProject() === $this) {
                $projectFinance->setProject(null);
            }
        }

        return $this;
    }

    public function getIsLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * @return Collection|Faq[]
     */
    public function getFaqs(): Collection
    {
        return $this->faqs;
    }

    public function addFaq(Faq $faq): self
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs[] = $faq;
            $faq->setProject($this);
        }

        return $this;
    }

    public function removeFaq(Faq $faq): self
    {
        if ($this->faqs->contains($faq)) {
            $this->faqs->removeElement($faq);
            // set the owning side to null (unless already changed)
            if ($faq->getProject() === $this) {
                $faq->setProject(null);
            }
        }

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }

    public function getPrints(): ?int
    {
        return $this->prints;
    }

    public function setPrints(int $prints): self
    {
        $this->prints = $prints;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setProject($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getProject() === $this) {
                $message->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MailSended[]
     */
    public function getMailSendeds(): Collection
    {
        return $this->mailSendeds;
    }

    public function addMailSended(MailSended $mailSended): self
    {
        if (!$this->mailSendeds->contains($mailSended)) {
            $this->mailSendeds[] = $mailSended;
            $mailSended->setProject($this);
        }

        return $this;
    }

    public function removeMailSended(MailSended $mailSended): self
    {
        if ($this->mailSendeds->contains($mailSended)) {
            $this->mailSendeds->removeElement($mailSended);
            // set the owning side to null (unless already changed)
            if ($mailSended->getProject() === $this) {
                $mailSended->setProject(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RequestDocumentation[]
     */
    public function getRequestDocumentations(): Collection
    {
        return $this->requestDocumentations;
    }

    public function addRequestDocumentation(RequestDocumentation $requestDocumentation): self
    {
        if (!$this->requestDocumentations->contains($requestDocumentation)) {
            $this->requestDocumentations[] = $requestDocumentation;
            $requestDocumentation->setProject($this);
        }

        return $this;
    }

    public function removeRequestDocumentation(RequestDocumentation $requestDocumentation): self
    {
        if ($this->requestDocumentations->contains($requestDocumentation)) {
            $this->requestDocumentations->removeElement($requestDocumentation);
            // set the owning side to null (unless already changed)
            if ($requestDocumentation->getProject() === $this) {
                $requestDocumentation->setProject(null);
            }
        }

        return $this;
    }

    public function canSeeDocumentation(User $user): bool
    {
        foreach ($this->requestDocumentations as $request) {
            if ($request->getUser() === $user && $request->getIsAccepted() === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return Collection|ProjectLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(ProjectLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setProject($this);
        }

        return $this;
    }

    public function removeLike(ProjectLike $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getProject() === $this) {
                $like->setProject(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user): bool
    {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) { return true; }
        }

        return false;
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

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setProject($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getProject() === $this) {
                $service->setProject(null);
            }
        }

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->metaTitle;
    }

    public function setMetaTitle(?string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }
}
