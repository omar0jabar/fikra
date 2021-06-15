<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SliderRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"imageName"})
 * @Vich\Uploadable
 */
class Slider
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageName;

    /**
     * @var File|null
     * @Assert\Image(
     *    mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="3M",
     *    minHeight="750",
     *    maxHeight="900",
     *    minWidth="1400",
     *    maxWidth="1600"
     * )
     * @Vich\UploadableField(mapping="slider", fileNameProperty="imageName")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageMobileName;

    /**
     * @var File|null
     * @Assert\Image(
     *      mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="3M",
     *    minHeight="400",
     *    maxHeight="500",
     *    minWidth="640",
     *    maxWidth="840"
     * )
     * @Vich\UploadableField(mapping="slider", fileNameProperty="imageMobileName")
     */
    private $imageMobileFile;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $colorText;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $content;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $button;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function __toString(): ?string
    {
        return (string)$this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * @return  File|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return Slider
     * @throws \Exception
     */
    public function setImageFile($imageFile): self
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    public function getImageMobileName(): ?string
    {
        return $this->imageMobileName;
    }

    public function setImageMobileName(?string $imageName): self
    {
        $this->imageMobileName = $imageName;

        return $this;
    }

    /**
     * @return  File|null
     */
    public function getImageMobileFile()
    {
        return $this->imageMobileFile;
    }

    /**
     * @param File|null $imageFile
     * @return Slider
     * @throws \Exception
     */
    public function setImageMobileFile($imageFile): self
    {
        $this->imageMobileFile = $imageFile;
        if ($this->imageMobileFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    public function getColorText(): ?string
    {
        return $this->colorText;
    }

    public function setColorText(?string $colorText): self
    {
        $this->colorText = $colorText;

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

    public function getButton(): ?string
    {
        return $this->button;
    }

    public function setButton(?string $button): self
    {
        $this->button = $button;

        return $this;
    }

    public function getLang(): ?string
    {
        return $this->lang;
    }

    public function setLang(string $lang): self
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
}
