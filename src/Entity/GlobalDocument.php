<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Cocur\Slugify\Slugify;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GlobalDocumentRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity(fields={"fileName"})
 */
class GlobalDocument
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
     */
    private $title;
    private $slugName;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
    * @var File|null
    * @Vich\UploadableField(mapping="documents", fileNameProperty="fileName")
    * @Assert\File(
    *    mimeTypes={"application/pdf",
     *                   "application/msword",
     *                   "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *                   "application/vnd.ms-excel",
     *                   "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
     *                     "image/jpeg", "image/png"},
    *    maxSize="10M",
    * )
    */
   private $uploadFile;

    /**
     * @ORM\Column(type="datetime", length=255)
     */
    private $createdAt;


    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $countDownload;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogDownload", mappedBy="document")
     */
    private $logDownloads;

    public function __construct()
    {
        $this->logDownloads = new ArrayCollection();
    }

    /**
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    */
    public function updatedTimestamps(): void
    {
      $dateTimeNow = new \DateTime('now');
        if ($this->getCreatedAt() === null) {
         $this->setCreatedAt($dateTimeNow);
        }
      $this->setUpdatedAt($dateTimeNow);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function __toString() {
        return (string) $this->fileName;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
    * @return  File|null
    */
   public function getUploadFile()
   {
      return $this->uploadFile;
   }

   /**
    * @param File|null $uploadFile
    * @return GlobalDocument
    * @throws \Exception
    */
   public function setUploadFile($uploadFile): self
   {
      $this->uploadFile = $uploadFile;
      if ($this->uploadFile instanceof UploadedFile) {
         $this->updatedAt = new \DateTime('now');
      }
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDocument() {
      return '/upload/documents/'.$this->fileName;
    }


    public function getSlugName(): ?string
    {
      $slugify = new Slugify();
      $title = $slugify->slugify($this->title).'-'.date('YmdHsi');
      return $title;
    }


    public function setSlugName(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCountDownload(): ?int
    {
        return $this->countDownload;
    }

    public function setCountDownload(?int $countDownload): self
    {
        $this->countDownload = $countDownload;

        return $this;
    }

    /**
     * @return Collection|LogDownload[]
     */
    public function getLogDownloads(): Collection
    {
        return $this->logDownloads;
    }

    public function addLogDownload(LogDownload $logDownload): self
    {
        if (!$this->logDownloads->contains($logDownload)) {
            $this->logDownloads[] = $logDownload;
            $logDownload->setDocument($this);
        }

        return $this;
    }

    public function removeLogDownload(LogDownload $logDownload): self
    {
        if ($this->logDownloads->contains($logDownload)) {
            $this->logDownloads->removeElement($logDownload);
            // set the owning side to null (unless already changed)
            if ($logDownload->getDocument() === $this) {
                $logDownload->setDocument(null);
            }
        }

        return $this;
    }

    public function getFileExtension()
    {
        $array = explode('.', $this->fileName);
        return end($array);
    }
}
