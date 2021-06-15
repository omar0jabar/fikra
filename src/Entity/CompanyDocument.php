<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompanyDocumentRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity(fields={"name"})
 */
class CompanyDocument
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
    * @Vich\UploadableField(mapping="company_document", fileNameProperty="name")
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
     * @ORM\Column(type="string", length=255)
     */
    private $type;


    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Company", inversedBy="documents")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $company;

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
    * @return CompanyDocument
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $documentType): self
    {
        $this->type = $documentType;

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $project): self
    {
        $this->company = $project;

        return $this;
    }
}
