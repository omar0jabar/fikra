<?php

namespace EgioDigital\CMSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="EgioDigital\CMSBundle\Repository\RowRepository")
 * @Orm\Table(name="`row`")
 */
class Row
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $indexRow;


    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $idCms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $idHtml;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCms(): ?int
    {
        return $this->idCms;
    }

    public function setIdCms(?int $idCms): self
    {
        $this->idCms = $idCms;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getIdHtml(): ?string
    {
        return $this->idHtml;
    }

    public function setIdHtml(string $idHtml): self
    {
        $this->idHtml = $idHtml;

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

    /**
     * @return mixed
     */
    public function getIndexRow()
    {
        return $this->indexRow;
    }

    public function setIndexRow(int $indexRow): self
    {
        $this->indexRow = $indexRow;

        return $this;
    }

}
