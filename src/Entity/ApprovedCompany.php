<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApprovedCompanyRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ApprovedCompany
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Company")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $company;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Domain", inversedBy="approvedCompanies")
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
     * @Assert\Regex(
     *     pattern="/^[0-9]+$/i",
     *     match=true,
     *     message="This field must contain just numbers"
     * )
     */
    private $fundingObjective;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApprovedUseFund", mappedBy="approvedCompany", orphanRemoval=true)
     */
    private $useOfFundsCollected;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $webSite;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApprovedCompanyDocument", mappedBy="approvedCompany", orphanRemoval=true)
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
     */
    private $slug;

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
    private $isDeleted;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="approvedCompanies")
     */
    private $city;

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
        $this->setUpdatedAt($dateTimeNow);
    }

    public function __construct()
    {
        $this->domain = new ArrayCollection();
        $this->useOfFundsCollected = new ArrayCollection();
        $this->documents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
    {
        return (string)$this->name;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
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

    public function setText(string $description): self
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
     * @return Collection|ApprovedUseFund[]
     */
    public function getUseOfFundsCollected(): Collection
    {
        return $this->useOfFundsCollected;
    }

    /**
     * @return array
     */
    public function getUseFundsArray(): array
    {
        $array = [];
        foreach ($this->useOfFundsCollected as $use) {
            $array[] = $use;
        }
        return $array;
    }

    public function addUseOfFundsCollected(ApprovedUseFund $useOfFundsCollected): self
    {
        if (!$this->useOfFundsCollected->contains($useOfFundsCollected)) {
            $this->useOfFundsCollected[] = $useOfFundsCollected;
            $useOfFundsCollected->setApprovedCompany($this);
        }

        return $this;
    }

    public function removeUseOfFundsCollected(ApprovedUseFund $useOfFundsCollected): self
    {
        if ($this->useOfFundsCollected->contains($useOfFundsCollected)) {
            $this->useOfFundsCollected->removeElement($useOfFundsCollected);
            // set the owning side to null (unless already changed)
            if ($useOfFundsCollected->getApprovedCompany() === $this) {
                $useOfFundsCollected->setApprovedCompany(null);
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
        return '/upload/company/approved-images/' . $this->logoName;
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
        return '/upload/company/approved-images/' . $this->coverName;
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
     * @return Collection|ApprovedCompanyDocument[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(ApprovedCompanyDocument $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setApprovedCompany($this);
        }

        return $this;
    }

    public function removeDocument(ApprovedCompanyDocument $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getApprovedCompany() === $this) {
                $document->setApprovedCompany(null);
            }
        }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    /**
     * @return bool|null
     */
    public function getIsClosed(): ?bool
    {
        return $this->getCompany()->getIsClosed();
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
        return $this->getCompany()->getNumberOfDays();
    }

    /**
     * @param string|null $status
     * @return int|null
     */
    public function getAmountCollected(string $status = null)
    {
        return $this->getCompany()->getAmountCollected($status);
    }

    /**
     * @return string
     */
    public function getPercentageOfContribution()
    {
        $amountCollected = $this->getAmountCollected('CONFIRMED');
        $objective = (int)$this->getFundingObjective();
        $percent = $amountCollected * 100 / $objective;
        return number_format($percent, 0, ",", " ") . '%';
    }

    /**
     * @param string|null $status
     * @return Collection|Contributor[]
     */
    public function getContributorsBuStatus(string $status = null): Collection
    {
        return $this->getCompany()->getContributorsByStatus($status);
    }

    public function getCommentsCountForFront()
    {
        $this->getCompany()->getCommentsCountForFront();
    }

    public function getConfirmedCount()
    {
        return $this->getCompany()->getConfirmedCount();
    }

}
