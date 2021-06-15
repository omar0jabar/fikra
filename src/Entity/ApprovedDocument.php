<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApprovedDocumentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ApprovedDocument
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrivate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DocumentType", inversedBy="approvedDocuments")
     */
    private $documentType;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\ApprovedProject", inversedBy="documents")
    * @ORM\JoinColumn(nullable=false, onDelete="cascade")
    */
   private $approvedProject;

    /**
     * @return mixed
     */
    public function getFileExtension()
    {
        $array = explode('.', $this->name);
        return end($array);
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    /**
     * @param bool|null $isPrivate
     * @return $this
     */
    public function setIsPrivate(?bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

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

    public function getDocumentType(): ?DocumentType
    {
        return $this->documentType;
    }

    public function setDocumentType(?DocumentType $documentType): self
    {
        $this->documentType = $documentType;

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
