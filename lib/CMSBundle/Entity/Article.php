<?php
namespace EgioDigital\CMSBundle\Entity;


use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity(fields={"title"})
 */
class Article
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
    * @Assert\Length(max="255")
    */
   private $title;

   /**
    * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\CategoryArticle", inversedBy="articles")
    */
   private $category;

   /**
    * @ORM\Column(type="string", nullable=false)
    * @Assert\Expression(
    *     "this.getLang() in ['en', 'fr']",
    * )
    */
   private $lang;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $slug;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $metaTitle;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $metaTags;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $metaDescription;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $htmlIdAttr;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $htmlClassAttr;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min=10)
     */
    private $content;


    /**
    * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\Block", orphanRemoval=true, mappedBy="article",cascade={"persist"})
    * @ORM\OrderBy({"position" = "asc"})
    */
   private $blocks;

   /**
    * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\ArticleView", mappedBy="article",cascade={"persist"})
    */
   private $views;

   /**
    * @ORM\Column(type="integer", nullable=false)
    */
   private $countViews;

   /**
    * @ORM\Column(type="boolean")
    */
   private $isActive;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $bannerDesktop;


   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $bannerMobile;

   /**
    * @var File|null
    * @Vich\UploadableField(mapping="articles", fileNameProperty="bannerDesktop")
    * @Assert\File(
    *    mimeTypes={"image/jpeg", "image/jpg", "image/png"},
    *    maxSize="3M",
    * )
    */
   private $uploadBannerDesktop;

   /**
    * @var File|null
    * @Vich\UploadableField(mapping="articles", fileNameProperty="bannerMobile")
    * @Assert\File(
    *    mimeTypes={"image/jpeg", "image/jpg", "image/png"},
    *    maxSize="3M",
    * )
    */
   private $uploadBannerMobile;

   /**
    * @ORM\Column(type="date", nullable=false)
    */
   private $dateTri;


   /**
    * @ORM\Column(type="datetime", nullable=false)
    */
   private $createdAt;

   /**
    * @ORM\Column(type="datetime", nullable=false)
    */
   private $updatedAt;

   public function __construct()
   {
      $this->blocks = new ArrayCollection();
      $this->views = new ArrayCollection();
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
      $slugify = new Slugify();
      if(empty($this->slug)) {
         $this->slug = $slugify->slugify($this->title);
      }
      if(empty($this->countViews)) {
         $this->countViews = 0;
      }
   }

   public function getId(): ?int
   {
      return $this->id;
   }

   public function __toString(): ?string
   {
      return (string)$this->title;
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

   public function getSlug(): ?string
   {
      return $this->slug;
   }

   public function setSlug(string $slug): self
   {
      $this->slug = $slug;

      return $this;
   }

   public function getMetaTitle(): ?string
   {
      return $this->metaTitle;
   }

   public function setMetaTitle(?string $metaTitle): self
   {
      $this->metaTitle = $metaTitle;

      return $this;
   }

   public function getMetaTags(): ?string
   {
      return $this->metaTags;
   }

   public function setMetaTags(?string $metaTags): self
   {
      $this->metaTags = $metaTags;

      return $this;
   }

   public function getMetaDescription(): ?string
   {
      return $this->metaDescription;
   }

   public function setMetaDescription(string $metaDescription): self
   {
      $this->metaDescription = $metaDescription;

      return $this;
   }

   public function getHtmlIdAttr(): ?string
   {
      return $this->htmlIdAttr;
   }

   public function setHtmlIdAttr(?string $htmlIdAttr): self
   {
      $this->htmlIdAttr = $htmlIdAttr;

      return $this;
   }

   public function getHtmlClassAttr(): ?string
   {
      return $this->htmlClassAttr;
   }

   public function setHtmlClassAttr(?string $htmlClassAttr): self
   {
      $this->htmlClassAttr = $htmlClassAttr;

      return $this;
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


    public function getIsActive(): ?bool
   {
      return $this->isActive;
   }

   public function setIsActive(bool $isActive): self
   {
      $this->isActive = $isActive;

      return $this;
   }

   public function getBannerDesktop(): ?string
   {
      return $this->bannerDesktop;
   }

   public function setBannerDesktop(?string $bannerDesktop): self
   {
      $this->bannerDesktop = $bannerDesktop;

      return $this;
   }

   /**
    * @return  File|null
    */
   public function getUploadBannerDesktop()
   {
      return $this->uploadBannerDesktop;
   }

   /**
    * @param File|null $uploadBannerDesktop
    * @return Article
    * @throws \Exception
    */
   public function setUploadBannerDesktop($uploadBannerDesktop): self
   {
      $this->uploadBannerDesktop = $uploadBannerDesktop;
      if ($this->uploadBannerDesktop instanceof UploadedFile) {
         $this->updatedAt = new \DateTime('now');
      }
      return $this;
   }

   public function getBannerMobile(): ?string
   {
      return $this->bannerMobile;
   }

   public function setBannerMobile(?string $bannerMobile): self
   {
      $this->bannerMobile = $bannerMobile;

      return $this;
   }

   /**
    * @return  File|null
    */
   public function getUploadBannerMobile()
   {
      return $this->uploadBannerMobile;
   }

   /**
    * @param File|null $uploadBannerMobile
    * @return Article
    * @throws \Exception
    */
   public function setUploadBannerMobile($uploadBannerMobile): self
   {
      $this->uploadBannerMobile = $uploadBannerMobile;
      if ($this->uploadBannerMobile instanceof UploadedFile) {
         $this->updatedAt = new \DateTime('now');
      }
      return $this;
   }

   public function getDateTri(): ?\DateTimeInterface
   {
      return $this->dateTri;
   }

   public function setDateTri(?\DateTimeInterface $dateTri): self
   {
      $this->dateTri = $dateTri;

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

   public function getCategory(): ?CategoryArticle
   {
      return $this->category;
   }

   public function setCategory(?CategoryArticle $category): self
   {
      $this->category = $category;

      return $this;
   }

   /**
    * @return Collection|Block[]
    */
   public function getBlocks(): Collection
   {
      return $this->blocks;
   }

   public function addBlock(Block $block): self
   {
      if (!$this->blocks->contains($block)) {
         $this->blocks[] = $block;
         $block->setArticle($this);
      }

      return $this;
   }

   public function removeBlock(Block $block): self
   {
      if ($this->blocks->contains($block)) {
         $this->blocks->removeElement($block);
         // set the owning side to null (unless already changed)
         if ($block->getArticle() === $this) {
            $block->setArticle(null);
         }
      }

      return $this;
   }


   /**
    * Clear id
    *
    * @return Article
    */
   public function clearId()
   {
      $this->id = null;

      return $this;
   }

   /**
    * @return Collection|ArticleView[]
    */
   public function getViews(): Collection
   {
      return $this->views;
   }

   public function addView(ArticleView $block): self
   {
      if (!$this->views->contains($block)) {
         $this->views[] = $block;
         $block->setArticle($this);
      }

      return $this;
   }

   public function removeView(ArticleView $block): self
   {
      if ($this->views->contains($block)) {
         $this->views->removeElement($block);
         // set the owning side to null (unless already changed)
         if ($block->getArticle() === $this) {
            $block->setArticle(null);
         }
      }

      return $this;
   }

   /**
    * @return mixed
    */
   public function getCountViews()
   {
      return $this->countViews;
   }

   /**
    * @param mixed $countViews
    */
   public function setCountViews($countViews): void
   {
      $this->countViews = $countViews;
   }

   /**
    * @return string|null
    */
   public function getLang(): ?string
   {
      return $this->lang;
   }


   public function setLang(?string $lang): ?self
   {
      $this->lang = $lang;

      return $this;
   }

}