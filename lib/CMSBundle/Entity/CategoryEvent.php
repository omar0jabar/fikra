<?php

namespace EgioDigital\CMSBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\CategoryEventRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"title"})
 */
class CategoryEvent
{
   /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Assert\Length(min=3, max="255")
    */
   private $title;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Assert\Expression(
    *     "this.getLang() in ['en', 'fr']",
    * )
    */
   private $lang;

   /**
    * @ORM\Column(type="datetime")
    */
   private $createdAt;

   /**
    * @ORM\Column(type="datetime")
    */
   private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\Event", mappedBy="category")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\EventPublished", mappedBy="category")
     */
    private $eventsPublished;

   public function __construct()
   {
      $this->events = new ArrayCollection();
      $this->eventsPublished = new ArrayCollection();
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
   }
   
   public function __toString(): ?string
    {
        return (string)$this->title;
    }
   public function getId(): ?int
   {
      return $this->id;
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

   public function getLang(): ?string
   {
      return $this->lang;
   }

   public function setLang(?string $lang): self
   {
      $this->lang = $lang;

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

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setCategory($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->contains($event)) {
            $this->events->removeElement($event);
            // set the owning side to null (unless already changed)
            if ($event->getCategory() === $this) {
                $event->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|EventPublished[]
     */
    public function getEventsPublished(): Collection
    {
        return $this->eventsPublished;
    }

    public function addEventPublished(EventPublished $eventPublished): self
    {
        if (!$this->eventsPublished->contains($eventPublished)) {
            $this->eventsPublished[] = $eventPublished;
            $eventPublished->setCategory($this);
        }

        return $this;
    }

    public function removeEventPublished(EventPublished $eventPublished): self
    {
        if ($this->eventsPublished->contains($eventPublished)) {
            $this->eventsPublished->removeElement($eventPublished);
            // set the owning side to null (unless already changed)
            if ($eventPublished->getCategory() === $this) {
                $eventPublished->setCategory(null);
            }
        }

        return $this;
    }
}
