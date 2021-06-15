<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentTypeRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"labelFr", "labelEn"})
 */
class DocumentType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *    max=255
     * )
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
     * @ORM\OneToMany(targetEntity="App\Entity\Document", mappedBy="documentType")
     */
    private $documents;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApprovedDocument", mappedBy="documentType")
     */
    private $approvedDocuments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\RequestDocumentation", mappedBy="documents")
     */
    private $requestDocumentations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RequestDocAccepted", mappedBy="type")
     */
    private $requestDocAccepteds;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $labelEn;

    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->approvedDocuments = new ArrayCollection();
        $this->requestDocumentations = new ArrayCollection();
        $this->requestDocAccepteds = new ArrayCollection();
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

    public function getLabel(): ?string
    {
        return $this->labelFr;
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
            $document->setDocumentType($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getDocumentType() === $this) {
                $document->setDocumentType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ApprovedDocument[]
     */
    public function getApprovedDocuments(): Collection
    {
        return $this->approvedDocuments;
    }

    public function addApprovedDocument(ApprovedDocument $approvedDocument): self
    {
        if (!$this->approvedDocuments->contains($approvedDocument)) {
            $this->approvedDocuments[] = $approvedDocument;
            $approvedDocument->setDocumentType($this);
        }

        return $this;
    }

    public function removeApprovedDocument(ApprovedDocument $approvedDocument): self
    {
        if ($this->approvedDocuments->contains($approvedDocument)) {
            $this->approvedDocuments->removeElement($approvedDocument);
            // set the owning side to null (unless already changed)
            if ($approvedDocument->getDocumentType() === $this) {
                $approvedDocument->setDocumentType(null);
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
            $requestDocumentation->addDocument($this);
        }

        return $this;
    }

    public function removeRequestDocumentation(RequestDocumentation $requestDocumentation): self
    {
        if ($this->requestDocumentations->contains($requestDocumentation)) {
            $this->requestDocumentations->removeElement($requestDocumentation);
            $requestDocumentation->removeDocument($this);
        }

        return $this;
    }

    /**
     * @return Collection|RequestDocAccepted[]
     */
    public function getRequestDocAccepteds(): Collection
    {
        return $this->requestDocAccepteds;
    }

    public function addRequestDocAccepted(RequestDocAccepted $requestDocAccepted): self
    {
        if (!$this->requestDocAccepteds->contains($requestDocAccepted)) {
            $this->requestDocAccepteds[] = $requestDocAccepted;
            $requestDocAccepted->setType($this);
        }

        return $this;
    }

    public function removeRequestDocAccepted(RequestDocAccepted $requestDocAccepted): self
    {
        if ($this->requestDocAccepteds->contains($requestDocAccepted)) {
            $this->requestDocAccepteds->removeElement($requestDocAccepted);
            // set the owning side to null (unless already changed)
            if ($requestDocAccepted->getType() === $this) {
                $requestDocAccepted->setType(null);
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
