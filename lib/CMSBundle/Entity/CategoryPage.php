<?php

namespace EgioDigital\CMSBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\CategoryPageRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"title"})
 */
class CategoryPage
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
    * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\Page", mappedBy="category")
    */
   private $pages;

   public function __construct()
   {
      $this->pages = new ArrayCollection();
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

   public function setTitle(string $title): self
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
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
   public function updatedTimestamps(): void
   {
      $dateTimeNow = new \DateTime('now');
      $this->setUpdatedAt($dateTimeNow);
      if ($this->getCreatedAt() === null) {
         $this->setCreatedAt($dateTimeNow);
      }
   }

   /**
    * @return Collection|Page[]
    */
   public function getPages(): Collection
   {
      return $this->pages;
   }

   public function addPage(Page $page): self
   {
      if (!$this->pages->contains($page)) {
         $this->pages[] = $page;
         $page->setCategory($this);
      }

      return $this;
   }

   public function removePage(Page $page): self
   {
      if ($this->pages->contains($page)) {
         $this->pages->removeElement($page);
         // set the owning side to null (unless already changed)
         if ($page->getCategory() === $this) {
            $page->setCategory(null);
         }
      }

      return $this;
   }
}
