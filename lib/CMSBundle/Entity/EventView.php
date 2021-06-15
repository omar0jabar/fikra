<?php
namespace EgioDigital\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\EventViewRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"ip"})
 */
class EventView
{
   /**
    * @ORM\Id()
    * @ORM\GeneratedValue()
    * @ORM\Column(type="integer")
    */
   private $id;

   /**
    * @ORM\Column(type="string", length=255)
    */
   private $ip;

   /**
    * @ORM\ManyToOne(targetEntity="EgioDigital\CMSBundle\Entity\EventPublished", inversedBy="views")
    * @ORM\JoinColumn(nullable=false, onDelete="cascade")
    */
   private $event;

   /**
    * @ORM\Column(type="datetime", nullable=false)
    */
   private $createdAt;

   /**
    * @ORM\PrePersist
    * @throws \Exception
    */
   public function updatedTimestamps(): void
   {
      $dateTimeNow = new \DateTime('now');
      if ($this->getCreatedAt() === null) {
         $this->setCreatedAt($dateTimeNow);
      }
   }

   public function getId(): ?int
   {
      return $this->id;
   }

   public function getIp(): ?string
   {
      return $this->ip;
   }

   public function setIp(string $ip): self
   {
      $this->ip = $ip;

      return $this;
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

   public function __toString()
   {
       return (string)$this->ip;
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

}