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
 * @ORM\Entity(repositoryClass="App\Repository\ToolsRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity(fields={"fileName"})
 */
class Tools
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
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileName;

    /**
    * @var File|null
    * @Vich\UploadableField(mapping="tools_documents", fileNameProperty="fileName")
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
     * @ORM\Column(type="string", length=255)
     */
    private $icon;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $countDownload;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LogDownload", mappedBy="tool")
     */
    private $logDownloads;

    public function __construct()
    {
        $this->logDownloads = new ArrayCollection();
    }

   /**
    * @ORM\PrePersist()
    * @ORM\PreUpdate()
    * @throws \Exception
    */
    public function updatedTimestamps(): void
    {
      $dateTimeNow = new \DateTime('now');
        if ($this->getCreatedAt() === null) {
         $this->setCreatedAt($dateTimeNow);
        }
      $this->setUpdatedAt($dateTimeNow);
    }

    public function __toString() {
        return (string) $this->title;
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
    * @return Tools
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

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
    
    //@TODO: check link
    public function getDocument() {
      return '/upload/tools/'.$this->fileName;
    }

    public function getLinkIcon() {
        switch ($this->icon) {
            case '0':
                return '/images/calcule.png';
                break;
            case '1':
                return '/images/money.png';
                break;
            case '2':
                return '/images/justice.png';
                break;
            
            default:
                # code...
                break;
        }
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
            $logDownload->setTool($this);
        }

        return $this;
    }

    public function removeLogDownload(LogDownload $logDownload): self
    {
        if ($this->logDownloads->contains($logDownload)) {
            $this->logDownloads->removeElement($logDownload);
            // set the owning side to null (unless already changed)
            if ($logDownload->getTool() === $this) {
                $logDownload->setTool(null);
            }
        }

        return $this;
    }
}
