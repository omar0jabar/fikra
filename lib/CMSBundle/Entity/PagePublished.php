<?php
namespace EgioDigital\CMSBundle\Entity;


use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection ;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\PagePublishedRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"title"})
 */
class PagePublished
{
   /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
   private $id;

    /**
     * @ORM\OneToOne(targetEntity="EgioDigital\CMSBundle\Entity\Page")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

   /**
    * @ORM\Column(type="string", length=255)
    * @Assert\NotBlank()
    * @Assert\Length(min=10, max="255")
    */
   private $title;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $slug;


   /**
    * @ORM\Column(type="string", length=255)
    */
   private $metaTitle;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $metaTags;

   /**
    * @ORM\Column(type="string", length=255)
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
    * @ORM\Column(type="boolean")
    */
   private $isActive;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isPageSimple;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $bannerDesktop;


   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    */
   private $bannerMobile;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min=10)
     */
    private $contentBanner;

   /**
    * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\CategoryPage", inversedBy="pages")
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
    * @ORM\Column(type="datetime", nullable=false)
    */
   private $createdAt;

   /**
    * @ORM\Column(type="datetime", nullable=false)
    */
   private $updatedAt;

   /**
    * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\BlockPublished", mappedBy="page", orphanRemoval=true, cascade={"persist"})
    * @ORM\OrderBy({"position" = "asc"})
    */
   private $blocks;


   public function __construct()
   {
      $this->blocks = new ArrayCollection();
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

      if (empty($this->slug)) {
         $this->slug = $slugify->slugify($this->title);
      }
      if (empty($this->isActive)) {
         $this->isActive = false;
      }
   }

   public function getId(): ?int
   {
      return $this->id;
   }

    public function getPage(): ?Page
    {
        return $this->page;
    }

    public function setPage(Page $page): self
    {
        $this->page = $page;

        return $this;
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

   public function setMetaTitle(string $metaTitle): self
   {
      $this->metaTitle = $metaTitle;

      return $this;
   }

   public function getMetaTags(): ?string
   {
      return $this->metaTags;
   }

   public function setMetaTags(string $metaTags): self
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

   public function getIsActive(): ?bool
   {
      return $this->isActive;
   }

   public function setIsActive(bool $isActive): self
   {
      $this->isActive = $isActive;

      return $this;
   }

    public function getIsPageSimple(): ?bool
    {
        return $this->isPageSimple;
    }

    public function setIsPageSimple(bool $isPageSimple): self
    {
        $this->isPageSimple = $isPageSimple;

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
    public function getBannerDesktop(): ?string
    {
        return $this->bannerDesktop;
    }

   public function setBannerDesktop(?string $bannerDesktop): self
   {
      $this->bannerDesktop = $bannerDesktop;

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

    public function getContentBanner(): ?string
    {
        return $this->contentBanner;
    }

    public function setContentBanner(?string $contentBanner): self
    {
        $this->contentBanner = $contentBanner;

        return $this;
    }

   public function getCategory(): ?CategoryPage
   {
      return $this->category;
   }

   public function setCategory(?CategoryPage $category): self
   {
      $this->category = $category;

      return $this;
   }

   /**
    * @return Collection|BlockPublished
    */
   public function getBlocks(): Collection
   {
      return $this->blocks;
   }

   public function addBlock(BlockPublished $block): self
   {
      if (!$this->blocks->contains($block)) {
         $this->blocks[] = $block;
         $block->setPage($this);
      }

      return $this;
   }

   public function removeBlock(BlockPublished $block): self
   {
      if ($this->blocks->contains($block)) {
         $this->blocks->removeElement($block);
         // set the owning side to null (unless already changed)
         if ($block->getPage() === $this) {
            $block->setPage(null);
         }
      }

      return $this;
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

   /**
    * Clear id
    *
    * @return PagePublished
    */
   public function clearId()
   {
      $this->id = null;

      return $this;
   }

}