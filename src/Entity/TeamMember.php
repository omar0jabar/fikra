<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamMemberRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity(fields={"photo"})
 */
class TeamMember
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
     * @Assert\Regex(
     *     pattern     = "/^[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]+$/i"
     * )
     * @Assert\Length(
     *   max="50"
     * )
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern     = "/^[a-zA-ZáàâäãåçéèêëíìîïñóòôöõúùûüýÿæœÁÀÂÄÃÅÇÉÈÊËÍÌÎÏÑÓÒÔÖÕÚÙÛÜÝŸÆŒ._\s-]+$/i"
     * )
     * @Assert\Length(
     *   max="50"
     * )
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

   /**
    * @var File|null
    * @Vich\UploadableField(mapping="member_photo", fileNameProperty="photo")
    * @Assert\Image(
    *    mimeTypes={"image/jpeg", "image/png"},
    *    maxSize="5M"
    * )
    */
   private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(
     *   max="100"
     * )
     */
    private $position;

   /**
    * @ORM\Column(type="boolean")
    */
   private $porteur;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *   max="400"
     * )
     */
    private $biography;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cv;

   /**
    * @var File|null
    * @Vich\UploadableField(mapping="member_cv", fileNameProperty="cv")
    * @Assert\File(
    *    mimeTypes={"application/pdf",
    *                "application/msword",
    *                "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
    *    },
    *    maxSize="5M",
    * )
    */
   private $cvFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $linkedin;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Url()
     */
    private $facebook;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="teamMembers")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $project;

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
      if ($this->getPorteur() === null) {
         $this->setPorteur(false);
      }
      $this->setUpdatedAt($dateTimeNow);
   }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

   public function __toString(): ?string
   {
      return (string)$this->firstName;
   }

   public function getFullName() {
      return "$this->firstName $this->lastName";
   }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

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
    * @return TeamMember
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

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;

        return $this;
    }

   /**
    * @return  File|null
    */
   public function getCvFile()
   {
      return $this->cvFile;
   }

   /**
    * @param File|null $cvFile
    * @return TeamMember
    * @throws \Exception
    */
   public function setCvFile($cvFile): self
   {
      $this->cvFile = $cvFile;
      if ($this->cvFile instanceof UploadedFile) {
         $this->updatedAt = new \DateTime('now');
      }
      return $this;
   }

    public function getLinkedin(): ?string
    {
        return $this->linkedin;
    }

    public function setLinkedin(?string $linkedin): self
    {
        $this->linkedin = $linkedin;

        return $this;
    }

    public function getTwitter(): ?string
    {
        return $this->twitter;
    }

    public function setTwitter(?string $twitter): self
    {
        $this->twitter = $twitter;

        return $this;
    }

    public function getFacebook(): ?string
    {
        return $this->facebook;
    }

    public function setFacebook(?string $facebook): self
    {
        $this->facebook = $facebook;

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

    public function getPorteur(): ?bool
    {
        return $this->porteur;
    }

    public function setPorteur(bool $porteur): self
    {
        $this->porteur = $porteur;

        return $this;
    }
}
