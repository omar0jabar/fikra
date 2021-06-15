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
 * @ORM\Entity(repositoryClass="App\Repository\CompanyRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"name"})
 * @UniqueEntity(fields={"RIB"})
 * @UniqueEntity(fields={"webSite"})
 * @UniqueEntity(fields={"slug"})
 * @Vich\Uploadable
 */
class Company
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Domain", inversedBy="companies")
     */
    private $domain;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $associationName;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(
     *     min="1",
     *     max="6"
     * )
     */
    private $duration;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="4",
     *     max="13"
     * )
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/i",
     *     match=true,
     *     message="This field must contain just numbers"
     * )
     */
    private $fundingObjective;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UseFund", mappedBy="company", orphanRemoval=true, cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $useOfFundsCollecteds;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="24",
     *     max="24"
     * )
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/i",
     *     match=true,
     *     message="This field must contain just numbers"
     * )
     */
    private $RIB;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="company_logo", fileNameProperty="logoName")
     * @Assert\Image(
     *    mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="1M"
     * )
     */
    private $logoFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="company_cover", fileNameProperty="coverName")
     * @Assert\Image(
     *    mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="2M"
     * )
     */
    private $coverFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $webSite;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompanyDocument", mappedBy="company", orphanRemoval=true, cascade={"persist"})
     * @Assert\Valid()
     */
    private $documents;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="companies")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/^[a-z0-9\-]+$/i"
     * )
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLegalRepresentativeOfTheAssociation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAcceptedTheConditionOfSecurity;

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
     * @ORM\OneToMany(targetEntity="App\Entity\CompanyLike", mappedBy="company", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prints;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metaTitle;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompanyComment", mappedBy="company")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contributor", mappedBy="company")
     */
    private $contributors;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="companies")
     */
    private $city;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ApprovedCompany")
     * @ORM\JoinColumn(nullable=true)
     */
    private $approvedCompany;

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
            $this->setIsDraft(true)
                ->setIsApproved(false)
                ->setIsDeleted(false)
                ->setIsLocked(false)
                ->setIsRejected(false)
                ->setIsUpdated(false)
                ->setIsVerified(false);
        }
        $this->setUpdatedAt($dateTimeNow);
    }

    public function __construct()
    {
        $this->domain = new ArrayCollection();
        $this->useOfFundsCollecteds = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->contributors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
    {
        return (string)$this->name;
    }

    public function getName(): ?string
    {
        return ucfirst($this->name);
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Domain[]
     */
    public function getDomain(): Collection
    {
        return $this->domain;
    }

    public function addDomain(Domain $domain): self
    {
        if (!$this->domain->contains($domain)) {
            $this->domain[] = $domain;
        }

        return $this;
    }

    public function removeDomain(Domain $domain): self
    {
        if ($this->domain->contains($domain)) {
            $this->domain->removeElement($domain);
        }

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $description): self
    {
        $this->text = $description;

        return $this;
    }

    public function getAssociationName(): ?string
    {
        return $this->associationName;
    }

    public function setAssociationName(string $associationName): self
    {
        $this->associationName = $associationName;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getFundingObjective(): ?float
    {
        return $this->fundingObjective;
    }

    public function setFundingObjective(float $fundingObjective): self
    {
        $this->fundingObjective = $fundingObjective;

        return $this;
    }

    /**
     * @return array
     */
    public function getUseFundsArray(): array
    {
        $array = [];
        foreach ($this->useOfFundsCollecteds as $use) {
            $array[] = $use;
        }
        return $array;
    }

    /**
     * @return Collection|UseFund[]
     */
    public function getUseOfFundsCollecteds(): Collection
    {
        return $this->useOfFundsCollecteds;
    }

    public function addUseOfFundsCollected(UseFund $useOfFundsCollected): self
    {
        if (!$this->useOfFundsCollecteds->contains($useOfFundsCollected)) {
            $this->useOfFundsCollecteds[] = $useOfFundsCollected;
            $useOfFundsCollected->setCompany($this);
        }

        return $this;
    }

    public function removeUseOfFundsCollected(UseFund $useOfFundsCollected): self
    {
        if ($this->useOfFundsCollecteds->contains($useOfFundsCollected)) {
            $this->useOfFundsCollecteds->removeElement($useOfFundsCollected);
            // set the owning side to null (unless already changed)
            if ($useOfFundsCollected->getCompany() === $this) {
                $useOfFundsCollected->setCompany(null);
            }
        }

        return $this;
    }

    public function getRIB(): ?string
    {
        return $this->RIB;
    }

    public function setRIB(string $RIB): self
    {
        $this->RIB = $RIB;

        return $this;
    }

    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    public function getLogoPath(): ?string
    {
        return '/upload/company/logo/' . $this->logoName;
    }

    public function setLogoName(?string $logoName): self
    {
        $this->logoName = $logoName;

        return $this;
    }

    public function getCoverName(): ?string
    {
        return $this->coverName;
    }

    public function getCoverPath(): ?string
    {
        return '/upload/company/cover/' . $this->coverName;
    }

    public function setCoverName(?string $coverName): self
    {
        $this->coverName = $coverName;

        return $this;
    }

    public function getWebSite(): ?string
    {
        return $this->webSite;
    }

    public function setWebSite(?string $webSite): self
    {
        $this->webSite = $webSite;

        return $this;
    }

    public function getUser(): ?\App\Entity\User
    {
        return $this->user;
    }

    public function setUser(?\App\Entity\User $user): self
    {
        $this->user = $user;

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
     * @return  File|null
     */
    public function getLogoFile()
    {
        return $this->logoFile;
    }

    /**
     * @param File|null $logoFile
     * @return Company
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

    /**
     * @return  File|null
     */
    public function getCoverFile()
    {
        return $this->coverFile;
    }

    /**
     * @param File|null $imageFile
     * @return Company
     * @throws \Exception
     */
    public function setCoverFile($imageFile): self
    {
        $this->coverFile = $imageFile;
        if ($this->coverFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
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

    public function getIsLegalRepresentativeOfTheAssociation(): ?bool
    {
        return $this->isLegalRepresentativeOfTheAssociation;
    }

    public function setIsLegalRepresentativeOfTheAssociation(bool $isLegalRepresentativeOfTheAssociation): self
    {
        $this->isLegalRepresentativeOfTheAssociation = $isLegalRepresentativeOfTheAssociation;

        return $this;
    }

    public function getIsAcceptedTheConditionOfSecurity(): ?bool
    {
        return $this->isAcceptedTheConditionOfSecurity;
    }

    public function setIsAcceptedTheConditionOfSecurity(bool $isAcceptedTheConditionOfSecurity): self
    {
        $this->isAcceptedTheConditionOfSecurity = $isAcceptedTheConditionOfSecurity;

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

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

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
     * @return Collection|CompanyLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(CompanyLike $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setCompany($this);
        }

        return $this;
    }

    public function removeLike(CompanyLike $like): self
    {
        if ($this->likes->contains($like)) {
            $this->likes->removeElement($like);
            // set the owning side to null (unless already changed)
            if ($like->getCompany() === $this) {
                $like->setCompany(null);
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

    public function isLikedByUser(User $user): bool
    {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) { return true; }
        }

        return false;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsClosed(): ?bool
    {
        try {
            $duration = $this->getDuration();
            if ($this->getApprovedCompany()) {
                $duration = $this->getApprovedCompany()->getDuration();
            }
            $endDate = $this->getEndDate();
            $now = new \DateTime();
            if ($endDate) {
                if ($now > $endDate) {
                    return true;
                }
                return false;
            }
            // count if closed from created date
            $createdAt = $this->getCreatedAt();
            $endDate = new \DateTime($createdAt->format("Y-m-d h:i:s"));
            $month = $duration > 1 ? $duration . ' months' : $duration .' month';
            $endDate->modify('+' . $month);
            date_time_set($endDate,23,59,59);
            if ($now > $endDate) {
                return true;
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getCommentsCountForFront()
    {
        $count = 0;
        foreach ($this->getComments() as $comment) {
            if ($comment->getIsPublished()) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * @return Collection|CompanyComment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(CompanyComment $companyComment): self
    {
        if (!$this->comments->contains($companyComment)) {
            $this->comments[] = $companyComment;
            $companyComment->setCompany($this);
        }

        return $this;
    }

    public function removeComment(CompanyComment $companyComment): self
    {
        if ($this->comments->contains($companyComment)) {
            $this->comments->removeElement($companyComment);
            // set the owning side to null (unless already changed)
            if ($companyComment->getCompany() === $this) {
                $companyComment->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contributor[]
     */
    public function getContributors(): Collection
    {
        return $this->contributors;
    }

    /**
     * @param string|null $status
     * @return ArrayCollection
     */
    public function getContributorsByStatus(string $status = null)
    {
        $contributors = new ArrayCollection();
        foreach ($this->getContributors() as $key => $contributor) {
            if ($contributor->getStatus() == $status) {
                $contributors->add($contributor);
            }
            if ($status == 'PENDING' && $contributor->getStatus() == null) {
                $contributors->add($contributor);
            }
        }
        return $contributors;
    }

    public function addContributor(Contributor $contributor): self
    {
        if (!$this->contributors->contains($contributor)) {
            $this->contributors[] = $contributor;
            $contributor->setCompany($this);
        }

        return $this;
    }

    public function removeContributor(Contributor $contributor): self
    {
        if ($this->contributors->contains($contributor)) {
            $this->contributors->removeElement($contributor);
            // set the owning side to null (unless already changed)
            if ($contributor->getCompany() === $this) {
                $contributor->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDocumentsArray(): array
    {
        $array = [];
        foreach ($this->documents as $document) {
            $array[] = $document;
        }
        return $array;
    }

    /**
     * @return Collection|CompanyDocument[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(CompanyDocument $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setCompany($this);
        }

        return $this;
    }

    public function removeDocument(CompanyDocument $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getCompany() === $this) {
                $document->setCompany(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNumberOfDays()
    {
        $startDay = new \DateTime();
        if ($startDay < $this->endDate && $startDay && $this->endDate) {
            $diff = $startDay->diff($this->endDate);
            return $diff->days;
        }
        return null;
    }

    /**
     * @param string|null $status
     * @return int|null
     */
    public function getAmountCollected(string $status = null)
    {
        $amountCollected = 0;
        if ($this->contributors->count() > 0 ) {
            /** @var Contributor $contributor */
            foreach ($this->contributors as $contributor) {
                if ($status) {
                    if ($contributor->getStatus() == $status) {
                        $amountCollected += $contributor->getContributionAmount();
                    }
                } else {
                    $amountCollected += $contributor->getContributionAmount();
                }
            }
            return $amountCollected;
        }
        return $amountCollected;
    }

    /**
     * @return string
     */
    public function getPercentageOfContribution()
    {
        if ($this->getApprovedCompany()) {
            return $this->getApprovedCompany()->getPercentageOfContribution();
        }
        return "0%";
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

    public function getConfirmedCount()
    {
        return $this->getContributorsByStatus('CONFIRMED')->count();
    }

}
