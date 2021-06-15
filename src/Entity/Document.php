<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity(fields={"name"})
 */
class Document
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
    * @var File|null
    * @Vich\UploadableField(mapping="project_documents", fileNameProperty="name")
    * @Assert\File(
    *    mimeTypes={"application/pdf",
    *                   "application/msword",
    *                   "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    *                   "application/vnd.ms-excel",
    *                   "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    *    },
    *    maxSize="3M",
    *    mimeTypesMessage="The type of your file is invalid. Allowed PDF or WORD or EXCEL."
    * )
    */
   private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DocumentType", inversedBy="documents")
     */
    private $documentType;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPrivate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $project;

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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

   public function __toString(): ?string
   {
      return (string)$this->name;
   }

   /**
    * @return  File|null
    */
   public function getFile()
   {
      return $this->file;
   }

   /**
    * @param File|null $file
    * @return Document
    * @throws \Exception
    */
   public function setFile($file): self
   {
      $this->file = $file;
      if ($this->file instanceof UploadedFile) {
         $this->updatedAt = new \DateTime('now');
      }
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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
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
}
