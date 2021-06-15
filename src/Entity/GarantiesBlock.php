<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GarantiesBlockRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class GarantiesBlock
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $introduction;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

   /**
    * @var File|null
    * @Assert\Image(
    *      mimeTypes={"image/jpeg", "image/png"},
    *    maxSize="3M"
    * )
    * @Vich\UploadableField(mapping="garanties_block", fileNameProperty="img")
    */
   private $imageFile;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

   /**
    * @ORM\Column(type="string", length=255)
    * @Assert\Expression(
    *     "this.getLang() in ['en', 'fr']",
    * )
    */
   private $lang;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slug;

   /**
    * @ORM\Column(type="datetime", nullable=true)
    */
   private $updatedAt;

   /**
    * @ORM\PreUpdate
    * @throws \Exception
    */
   public function updatedTimestamps(): void
   {
      $dateTimeNow = new \DateTime('now');
      $this->setUpdatedAt($dateTimeNow);
   }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

   public function __toString(): ?string
   {
      return (string)$this->title;
   }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

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
    * @return GarantiesBlock
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

   public function getLang(): ?string
   {
      return $this->lang;
   }

   public function setLang(string $lang): self
   {
      $this->lang = $lang;

      return $this;
   }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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
}
