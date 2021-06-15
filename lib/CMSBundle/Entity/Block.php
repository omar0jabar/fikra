<?php

namespace EgioDigital\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\BlockRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class Block
{
   /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\Column(type="integer")
    */
   private $colLarge;


   /**
    * @ORM\Column(type="boolean")
    */
   private $clearfix;

   /**
    * @ORM\Column(type="datetime")
    */
   private $createdAt;

   /**
    * @ORM\Column(type="datetime")
    */
   private $updatedAt;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $title;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $linkVideo;

   /**
    * @ORM\Column(type="text", nullable=true)
    */
   private $content;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $alt;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $imageName;

   /**
    * @var File|null
    * @Vich\UploadableField(mapping="blocks", fileNameProperty="imageName")
    *
    */
   private $uploadImage;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textImage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $legend;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkImage;

   /**
    * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\Page", inversedBy="blocks")
    */
   private $page;

   /**
    * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\Article", inversedBy="blocks")
    */
   private $article;

    /**
     * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\Event", inversedBy="blocks")
     */
    private $event;

   /**
    * @ORM\Column(type="integer" , options={"unsigned":true, "default":0})
    */
   private $position;

   /**
    * @ORM\Column(type="text", nullable=true)
    * @Assert\Choice({"image", "video", "text"})
    */
   private $type;

   /**
    * @ORM\Column(type="integer")
    */
   private $width;

   /**
    * @ORM\Column(type="integer")
    */
   private $height;

   /**
    * @ORM\Column(type="integer" , options={"unsigned":true, "default":0}, name="`row`")
    */
   private $row;

   /**
    * @return mixed
    */
   public function getWidth(): ?int
   {
      return $this->width;
   }

   public function setWidth(?int $width): self
   {
      $this->width = $width;
      return $this;
   }

   /**
    * @return mixed
    */
   public function getHeight(): ?int
   {
      return $this->height;
   }


   public function setHeight(?int $height): self
   {
      $this->height = $height;

      return $this ;
   }


   public function __toString(): ?string
   {
      return (string)$this->title;
   }

   /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    * @throws \Exception
    */
   public function updatedTimestamps(): void
   {
      $dateTimeNow = new \DateTime('now');
      $this->setUpdatedAt($dateTimeNow);
      if ($this->getCreatedAt() === null) {
         $this->setCreatedAt($dateTimeNow);
      }

      if(empty($this->height)) {
         $this->height = 10;
      }
      if(empty($this->width)) {
         $this->width = 10;
      }
   }

   public function getContent(): ?string
   {
      return $this->content;
   }

   public function setContent(?string $content): self
   {
      $this->content = $content;

      return $this;
   }

   public function getId(): ?int
   {
      return $this->id;
   }


   public function getColLarge(): ?int
   {
      return $this->colLarge;
   }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

   public function setColLarge(int $colLarge): self
   {
      $this->colLarge = $colLarge;

      return $this;
   }

   public function getClearfix(): ?bool
   {
      return $this->clearfix;
   }

   public function setClearfix(bool $clearfix): self
   {
      $this->clearfix = $clearfix;

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

   public function getTitle(): ?string
   {
      return $this->title;
   }

   public function setTitle(?string $title): self
   {
      $this->title = $title;

      return $this;
   }

   public function getAlt(): ?string
   {
      return $this->alt;
   }

   public function setAlt(?string $alt): self
   {
      $this->alt = $alt;

      return $this;
   }

   public function getLinkVideo(): ?string
   {
      return $this->linkVideo;
   }

   public function getIdVideo(): ?string
   {
      parse_str( parse_url( $this->linkVideo, PHP_URL_QUERY ), $my_array_of_vars );
      return isset($my_array_of_vars['v']) ? $my_array_of_vars['v'] : $this->linkVideo;
   }

   /**
    * @param string|null $linkVideo
    * @return Block
    */
   public function setLinkVideo(?string $linkVideo): self
   {
      //parse_str( parse_url( $linkVideo, PHP_URL_QUERY ), $my_array_of_vars );
      //$this->linkVideo = $my_array_of_vars['v'];
      // just for data fixtures
      $this->linkVideo = $linkVideo;

      return $this;
   }

   public function getPage(): ?Page
   {
      return $this->page;
   }

   public function setPage(?Page $page): self
   {
      $this->page = $page;

      return $this;
   }

   public function getArticle(): ?Article
   {
      return $this->article;
   }

   public function setArticle(?Article $article): self
   {
      $this->article = $article;

      return $this;
   }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

   public function getPosition(): ?int
   {
      return $this->position;
   }

   public function setPosition(int $position): self
   {
      $this->position = $position;

      return $this;
   }

   public function getRow(): ?int
   {
      return $this->row;
   }

   public function setRow(int $row): self
   {
      $this->row = $row;

      return $this;
   }

   /**
    * @return  File|null
    */
   public function getUploadImage()
   {
      return $this->uploadImage;
   }

   /**
    * @param File|null $uploadImage
    * @return Block
    * @throws \Exception
    */
   public function setUploadImage($uploadImage): self
   {
      $this->uploadImage = $uploadImage;
      if ($this->uploadImage instanceof UploadedFile) {
         $this->updatedAt = new \DateTime('now');
      }
      return $this;
   }

    public function getTextImage(): ?string
    {
        return $this->textImage;
    }

    public function setTextImage(?string $textImage): self
    {
        $this->textImage = $textImage;

        return $this;
    }

    public function getLegend(): ?string
    {
        return $this->legend;
    }

    public function setLegend(?string $legend): self
    {
        $this->legend = $legend;

        return $this;
    }

    public function getLinkImage(): ?string
    {
        return $this->linkImage;
    }

    public function setLinkImage(?string $linkImage): self
    {
        $this->linkImage = $linkImage;

        return $this;
    }

   public function getType(): ?string
   {
      return $this->type;
   }

   public function setType(?string $type): self
   {
      $this->type = $type;

      return $this;
   }

   /**
    * Clear id
    *
    * @return Block
    */
   public function clearId()
   {
      $this->id = null;

      return $this;
   }

}
