<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentCaMarcheBlockRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class CommentCaMarcheBlock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

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
    * @Vich\UploadableField(mapping="comment_ca_marche_img", fileNameProperty="img")
    */
   private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $btn1Text;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $btn1Link;

   /**
    * @ORM\Column(type="string", length=255)
    * @Assert\Expression(
    *     "this.getBtn1Target() in ['_blank', '_self']",
    * )
    */
   private $btn1Target;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $btn2Text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $btn2Link;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Assert\Expression(
    *     "this.getBtn2Target() in ['_blank', '_self']",
    * )
    */
   private $btn2Target;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Expression(
     *     "this.getLang() in ['en', 'fr']",
     * )
     */
    private $lang;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $backgroundColor;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $type;

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
    * @return CommentCaMarcheBlock
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

   public function __toString(): ?string
   {
      return (string)$this->title;
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

    public function getBtn1Text(): ?string
    {
        return $this->btn1Text;
    }

    public function setBtn1Text(string $btn1Text): self
    {
        $this->btn1Text = $btn1Text;

        return $this;
    }

    public function getBtn1Link(): ?string
    {
        return $this->btn1Link;
    }

    public function setBtn1Link(string $btn1Link): self
    {
        $this->btn1Link = $btn1Link;

        return $this;
    }

   public function getBtn1Target(): ?string
   {
      return $this->btn1Target;
   }

   public function setBtn1Target(string $btn1Target): self
   {
      $this->btn1Target = $btn1Target;

      return $this;
   }

    public function getBtn2Text(): ?string
    {
        return $this->btn2Text;
    }

    public function setBtn2Text(string $btn2Text): self
    {
        $this->btn2Text = $btn2Text;

        return $this;
    }

    public function getBtn2Link(): ?string
    {
        return $this->btn2Link;
    }

    public function setBtn2Link(string $btn2Link): self
    {
        $this->btn2Link = $btn2Link;

        return $this;
    }

   public function getBtn2Target(): ?string
   {
      return $this->btn2Target;
   }

   public function setBtn2Target(string $btn2Target): self
   {
      $this->btn2Target = $btn2Target;

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

    public function getBackgroundColor(): ?string
    {
        return $this->backgroundColor;
    }

    public function setBackgroundColor(string $backgroundColor): self
    {
        $this->backgroundColor = $backgroundColor;

        return $this;
    }

   public function getType(): ?string
   {
      return $this->type;
   }

   public function setType(string $type): self
   {
      $this->type = $type;

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
