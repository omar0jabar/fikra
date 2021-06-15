<?php
namespace EgioDigital\CMSBundle\Entity;

use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\EventPublishedRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"title"})
 */
class EventPublished
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
     * @Assert\Length(min=10, max="255")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\CategoryEvent", inversedBy="eventsPublished")
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
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lieu;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDebut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFin;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureDebut;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $heureFin;

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
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\BlockPublished", orphanRemoval=true, mappedBy="event")
     * @ORM\OrderBy({"position" = "asc"})
     */
    private $blocks;

    /**
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\EventView", mappedBy="event",cascade={"persist"})
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
     * @ORM\OneToOne(targetEntity="EgioDigital\CMSBundle\Entity\Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\EventLike", mappedBy="event",cascade={"persist"})
     */
    private $likes;

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
        $this->likes = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }


    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }


    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getHeureDebut(): ?\DateTimeInterface
    {
        return $this->heureDebut;
    }

    public function setHeureDebut(?\DateTimeInterface $time): self
    {
        $this->heureDebut = $time;

        return $this;
    }

    public function getHeureFin(): ?\DateTimeInterface
    {
        return $this->heureFin;
    }

    public function setHeureFin(?\DateTimeInterface $time): self
    {
        $this->heureFin = $time;

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

    public function setMetaDescription(?string $metaDescription): self
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

    public function getBannerMobile(): ?string
    {
        return $this->bannerMobile;
    }

    public function setBannerMobile(?string $bannerMobile): self
    {
        $this->bannerMobile = $bannerMobile;

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

    public function getCategory(): ?CategoryEvent
    {
        return $this->category;
    }

    public function setCategory(?CategoryEvent $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|BlockPublished[]
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(BlockPublished $block): self
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks[] = $block;
            $block->setEvent($this);
        }

        return $this;
    }

    public function removeBlock(BlockPublished $block): self
    {
        if ($this->blocks->contains($block)) {
            $this->blocks->removeElement($block);
            // set the owning side to null (unless already changed)
            if ($block->getEvent() === $this) {
                $block->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * Clear id
     *
     * @return EventPublished
     */
    public function clearId()
    {
        $this->id = null;

        return $this;
    }

    /**
     * @return Collection|EventView[]
     */
    public function getViews(): Collection
    {
        return $this->views;
    }

    public function addView(EventView $view): self
    {
        if (!$this->views->contains($view)) {
            $this->views[] = $view;
            $view->setEvent($this);
        }

        return $this;
    }

    public function removeView(EventView $view): self
    {
        if ($this->views->contains($view)) {
            $this->views->removeElement($view);
            // set the owning side to null (unless already changed)
            if ($view->getEvent() === $this) {
                $view->setEvent(null);
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

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    /**
     * @return Collection|EventLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(EventLike $eventLike): self
    {
        if (!$this->likes->contains($eventLike)) {
            $this->likes[] = $eventLike;
            $eventLike->setEvent($this);
        }

        return $this;
    }

    public function removeLike(EventLike $eventLike): self
    {
        if ($this->likes->contains($eventLike)) {
            $this->likes->removeElement($eventLike);
            // set the owning side to null (unless already changed)
            if ($eventLike->getEvent() === $this) {
                $eventLike->setEvent(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(User $user): bool
    {
        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) { return true; }
        }

        return false;
    }

}