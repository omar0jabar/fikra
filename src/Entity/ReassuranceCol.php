<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReassuranceColRepository")
 * @UniqueEntity(fields={"imgName"})
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable
 */
class ReassuranceCol
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $col;

    /**
     * @var File|null
     * @Assert\Image(
     *    mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="3M",
     * )
     * @Vich\UploadableField(mapping="reassurance", fileNameProperty="imgName")
     */
    private $imgFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Reassurance", inversedBy="cols")
     */
    private $reassurance;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): ?string
    {
        return (string)$this->col;
    }

    public function getCol(): ?string
    {
        return $this->col;
    }

    public function setCol(?string $col): self
    {
        $this->col = $col;

        return $this;
    }

    /**
     * @return  File|null
     */
    public function getImgFile()
    {
        return $this->imgFile;
    }

    /**
     * @param File|null $imageFile
     * @return self
     * @throws \Exception
     */
    public function setImgFile($imageFile): self
    {
        $this->imgFile = $imageFile;
        if ($this->imgFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    public function getImgName(): ?string
    {
        return $this->imgName;
    }

    public function setImgName(?string $imgName): self
    {
        $this->imgName = $imgName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getReassurance(): ?Reassurance
    {
        return $this->reassurance;
    }

    public function setReassurance(?Reassurance $reassurance): self
    {
        $this->reassurance = $reassurance;

        return $this;
    }
}
