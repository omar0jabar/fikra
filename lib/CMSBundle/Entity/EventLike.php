<?php

namespace EgioDigital\CMSBundle\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\EventLikeRepository")
 */
class EventLike
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\EventPublished", inversedBy="likes")
     * @ORM\JoinColumn(nullable=false, onDelete="cascade")
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likes")
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?EventPublished
    {
        return $this->event;
    }

    public function setEvent(?EventPublished $eventPublished): self
    {
        $this->event = $eventPublished;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
