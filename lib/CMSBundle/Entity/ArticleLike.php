<?php

namespace EgioDigital\CMSBundle\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\ArticleLikeRepository")
 */
class ArticleLike
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\ArticlePublished", inversedBy="likes")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likes")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?ArticlePublished
    {
        return $this->article;
    }

    public function setArticle(?ArticlePublished $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $article): self
    {
        $this->user = $article;

        return $this;
    }
}
