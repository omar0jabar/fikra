<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequestDocAcceptedRepository")
 */
class RequestDocAccepted
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\RequestDocumentation", inversedBy="docAccepteds")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $request;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DocumentType", inversedBy="requestDocAccepteds")
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRequest(): ?RequestDocumentation
    {
        return $this->request;
    }

    public function setRequest(?RequestDocumentation $request): self
    {
        $this->request = $request;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?DocumentType
    {
        return $this->type;
    }

    public function setType(?DocumentType $type): self
    {
        $this->type = $type;

        return $this;
    }
}
