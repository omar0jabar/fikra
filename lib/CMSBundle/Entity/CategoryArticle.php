<?php

namespace EgioDigital\CMSBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\CategoryArticleRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"title"})
 */
class CategoryArticle
{
   /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    * @Groups({"articles_read"})
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Assert\Length(min=3, max="255")
    * @Groups({"articles_read"})
    */
   private $title;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Assert\Expression(
    *     "this.getLang() in ['en', 'fr']",
    * )
    * @Groups({"articles_read"})
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
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\Article", mappedBy="category")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\ArticlePublished", mappedBy="category")
     */
    private $articlesPublished;

   public function __construct()
   {
      $this->articles = new ArrayCollection();
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
     * @return Collection|Article[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setCategory($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->contains($article)) {
            $this->articles->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|ArticlePublished[]
     */
    public function getArticlesPublished(): Collection
    {
        return $this->articlesPublished;
    }

    public function addArticlePublished(ArticlePublished $article): self
    {
        if (!$this->articlesPublished->contains($article)) {
            $this->articlesPublished[] = $article;
            $article->setCategory($this);
        }

        return $this;
    }

    public function removeArticlePublished(ArticlePublished $article): self
    {
        if ($this->articlesPublished->contains($article)) {
            $this->articlesPublished->removeElement($article);
            // set the owning side to null (unless already changed)
            if ($article->getCategory() === $this) {
                $article->setCategory(null);
            }
        }

        return $this;
    }
}
