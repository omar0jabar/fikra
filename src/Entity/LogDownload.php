<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LogDownloadRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class LogDownload
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
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GlobalDocument", inversedBy="logDownloads")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $document;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tools", inversedBy="logDownloads")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $tool;

    /**
     * @ORM\Column(type="datetime")
     */
    private $downloadedAt;

    /**
     * @ORM\PrePersist()
     */
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new \DateTime('now');
        if ($this->getDownloadedAt() === null) {
            $this->setDownloadedAt($dateTimeNow);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDocument(): ?GlobalDocument
    {
        return $this->document;
    }

    public function setDocument(?GlobalDocument $document): self
    {
        $this->document = $document;

        return $this;
    }

    public function getTool(): ?Tools
    {
        return $this->tool;
    }

    public function setTool(?Tools $tool): self
    {
        $this->tool = $tool;

        return $this;
    }

    public function getDownloadedAt(): ?\DateTimeInterface
    {
        return $this->downloadedAt;
    }

    public function setDownloadedAt(\DateTimeInterface $downloadedAt): self
    {
        $this->downloadedAt = $downloadedAt;

        return $this;
    }
}
