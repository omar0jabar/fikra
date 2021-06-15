<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BackgroundSliderRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"bannerMobile", "bannerDesktop"})
 * @Vich\Uploadable
 */
class BackgroundSlider
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
    private $bannerMobile;

    /**
     * @var File|null
     * @Assert\Image(
     *      mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="3M"
     * )
     * @Vich\UploadableField(mapping="slider", fileNameProperty="bannerMobile")
     */
    private $bannerMobileFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bannerDesktop;

    /**
     * @var File|null
     * @Assert\Image(
     *      mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="3M"
     * )
     * @Vich\UploadableField(mapping="slider", fileNameProperty="bannerDesktop")
     */
    private $bannerDesktopFile;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Expression(
     *     "this.getLanguage() in ['en', 'fr']",
     * )
     */
    private $language;

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

    public function getBannerMobile(): ?string
    {
        return $this->bannerMobile;
    }

    public function setBannerMobile(?string $bannerMobile): self
    {
        $this->bannerMobile = $bannerMobile;

        return $this;
    }

    /**
     * @return  File|null
     */
    public function getBannerMobileFile()
    {
        return $this->bannerMobileFile;
    }

    /**
     * @param File|null $imageFile
     * @return BackgroundSlider
     * @throws \Exception
     */
    public function setBannerMobileFile($imageFile): self
    {
        $this->bannerMobileFile = $imageFile;
        if ($this->bannerMobileFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
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

    /**
     * @return  File|null
     */
    public function getBannerDesktopFile()
    {
        return $this->bannerDesktopFile;
    }

    /**
     * @param File|null $imageFile
     * @return BackgroundSlider
     * @throws \Exception
     */
    public function setBannerDesktopFile($imageFile): self
    {
        $this->bannerDesktopFile = $imageFile;
        if ($this->bannerDesktopFile instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;

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
