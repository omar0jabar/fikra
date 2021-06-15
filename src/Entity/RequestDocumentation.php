<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequestDocumentationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RequestDocumentation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="requestDocumentations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="requestDocumentations")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $project;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAccepted;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DocumentType", inversedBy="requestDocumentations")
     */
    private $documents;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $AcceptedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RequestDocAccepted", mappedBy="request")
     */
    private $docAccepteds;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->docAccepteds = new ArrayCollection();
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
        $this->setUpdatedAt($dateTimeNow);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): ?string
    {
        return (string)$this->user;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

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

    public function getIsAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(?bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * @return Collection|DocumentType[]
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(DocumentType $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
        }

        return $this;
    }

    public function removeDocument(DocumentType $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
        }

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getAcceptedAt(): ?\DateTimeInterface
    {
        return $this->AcceptedAt;
    }

    public function setAcceptedAt(?\DateTimeInterface $AcceptedAt): self
    {
        $this->AcceptedAt = $AcceptedAt;

        return $this;
    }

    /**
     * @return Collection|RequestDocAccepted[]
     */
    public function getDocAccepteds(): Collection
    {
        return $this->docAccepteds;
    }

    public function addDocAccepted(RequestDocAccepted $docAccepted): self
    {
        if (!$this->docAccepteds->contains($docAccepted)) {
            $this->docAccepteds[] = $docAccepted;
            $docAccepted->setRequest($this);
        }

        return $this;
    }

    public function removeDocAccepted(RequestDocAccepted $docAccepted): self
    {
        if ($this->docAccepteds->contains($docAccepted)) {
            $this->docAccepteds->removeElement($docAccepted);
            // set the owning side to null (unless already changed)
            if ($docAccepted->getRequest() === $this) {
                $docAccepted->setRequest(null);
            }
        }

        return $this;
    }
}
