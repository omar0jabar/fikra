<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Cocur\Slugify\Slugify;


/**
 * @ORM\Entity(repositoryClass="App\Repository\FondRepository")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @UniqueEntity(fields={"title"})
 */
class Fond
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
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FondType", inversedBy="fonds")
     */
    private $fondType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Gestionnaire", inversedBy="fonds")
     */
    private $gestionnaire;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Gestionnaire", inversedBy="fonds")
     */
    private $gestionnaires;



    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EligibiliteCritere", inversedBy="fonds")
     */
    private $eligibiliteCritere;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DepensesType", inversedBy="fonds")
     */
    private $depensesType;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $local;

    /**
     * @ORM\Column(type="text")
     */
    private $sortDesctiption;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
    * @var File|null
    * @Vich\UploadableField(mapping="fonds", fileNameProperty="logo")
    * @Assert\File(
    *    mimeTypes={"image/jpeg", "image/png"},
    *    maxSize="10M",
    * )
    */
   private $uploadLogo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $img;

    /**
    * @var File|null
    * @Vich\UploadableField(mapping="fonds", fileNameProperty="img")
    * @Assert\File(
    *    mimeTypes={"image/jpeg", "image/jpg", "image/png"},
    *    maxSize="10M",
    * )
    */
    private $uploadImg;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Secteur", inversedBy="fondsSecteur")
     */
    private $secteurType;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Montant", inversedBy="fonds")
     */
    private $min;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Montant", inversedBy="fondsMax")
     */
    private $max;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Phase", inversedBy="fonds")
     */
    private $fondPhases;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mail;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FinancementType", inversedBy="fondsFinancement")
     */
    private $financements;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\DepensesType", inversedBy="depenseFonds")
     */
    private $depenses;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->secteurType = new ArrayCollection();
        $this->gestionnaires = new ArrayCollection();
        $this->fondPhases = new ArrayCollection();
        $this->financements = new ArrayCollection();
        $this->depenses = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @throws \Exception
     */
    public function updatedTimestamps(): void
    {
        $slugify = new Slugify();
        $this->setSlug($slugify->slugify($this->title));
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

    public function getFondType(): ?FondType
    {
        return $this->fondType;
    }

    public function setFondType(?FondType $fondType): self
    {
        $this->fondType = $fondType;

        return $this;
    }

    public function getGestionnaire(): ?Gestionnaire
    {
        return $this->gestionnaire;
    }

    public function setGestionnaire(?Gestionnaire $gestionnaire): self
    {
        $this->gestionnaire = $gestionnaire;

        return $this;
    }

    public function getEligibiliteCritere(): ?EligibiliteCritere
    {
        return $this->eligibiliteCritere;
    }

    public function setEligibiliteCritere(?EligibiliteCritere $eligibiliteCritere): self
    {
        $this->eligibiliteCritere = $eligibiliteCritere;

        return $this;
    }

    public function getDepensesType(): ?DepensesType
    {
        return $this->depensesType;
    }

    public function setDepensesType(?DepensesType $depensesType): self
    {
        $this->depensesType = $depensesType;

        return $this;
    }

    
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getLocal(): ?string
    {
        return $this->local;
    }

    public function setLocal(string $local): self
    {
        $this->local = $local;

        return $this;
    }

    public function getSortDesctiption(): ?string
    {
        return $this->sortDesctiption;
    }

    public function setSortDesctiption(string $sortDesctiption): self
    {
        $this->sortDesctiption = $sortDesctiption;

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

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        //if ($logo != null) {
            $this->logo = $logo;
        //}
        return $this;
    }

    /**
    * @return  File|null
    */
    public function getUploadLogo()
    {
      return $this->uploadLogo;
    }

   /**
    * @param File|null $uploadLogo
    * @return Fond
    * @throws \Exception
    */
   public function setUploadLogo($uploadLogo): self
   {
      $this->uploadLogo = $uploadLogo;
      if ($this->uploadLogo instanceof UploadedFile) {
         $this->updatedAt = new \DateTime('now');
      }
      return $this;
   }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }


    /**
    * @return  File|null
    */
    public function getUploadImg()
    {
      return $this->uploadImg;
    }

   /**
    * @param File|null $uploadImg
    * @return Fond
    * @throws \Exception
    */
   public function setUploadImg($uploadImg): self
   {
        $this->uploadImg = $uploadImg;
        if ($this->uploadImg instanceof UploadedFile) {
            $this->updatedAt = new \DateTime('now');
        }
        return $this;
   }

    /**
     * @return Collection|Secteur[]
     */
    public function getSecteurType(): Collection
    {
        return $this->secteurType;
    }

    public function addSecteurType(Secteur $secteurType): self
    {
        if (!$this->secteurType->contains($secteurType)) {
            $this->secteurType[] = $secteurType;
        }

        return $this;
    }

    public function removeSecteurType(Secteur $secteurType): self
    {
        if ($this->secteurType->contains($secteurType)) {
            $this->secteurType->removeElement($secteurType);
        }

        return $this;
    }

    /**
     * @return Collection|Secteur[]
     */
    public function getGestionnaires(): Collection
    {
        return $this->gestionnaires;
    }

    public function addGestionnaire(Gestionnaire $gestionnaires): self
    {
        if (!$this->gestionnaires->contains($gestionnaires)) {
            $this->gestionnaires[] = $gestionnaires;
        }

        return $this;
    }

    public function removeGestionnaire(Gestionnaire $gestionnaires): self
    {
        if ($this->gestionnaires->contains($gestionnaires)) {
            $this->gestionnaires->removeElement($gestionnaires);
        }

        return $this;
    }

    public function getMin(): ?Montant
    {
        return $this->min;
    }

    public function setMin(?Montant $min): self
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?Montant
    {
        return $this->max;
    }

    public function setMax(?Montant $max): self
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @return Collection|Phase[]
     */
    public function getFondPhases(): Collection
    {
        return $this->fondPhases;
    }

    public function addFondPhase(Phase $fondPhase): self
    {
        if (!$this->fondPhases->contains($fondPhase)) {
            $this->fondPhases[] = $fondPhase;
        }

        return $this;
    }

    public function removeFondPhase(Phase $fondPhase): self
    {
        if ($this->fondPhases->contains($fondPhase)) {
            $this->fondPhases->removeElement($fondPhase);
        }

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(?string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return Collection|FinancementType[]
     */
    public function getFinancements(): Collection
    {
        return $this->financements;
    }

    public function addFinancement(FinancementType $financement): self
    {
        if (!$this->financements->contains($financement)) {
            $this->financements[] = $financement;
        }

        return $this;
    }

    public function removeFinancement(FinancementType $financement): self
    {
        if ($this->financements->contains($financement)) {
            $this->financements->removeElement($financement);
        }

        return $this;
    }

    /**
     * @return Collection|DepensesType[]
     */
    public function getDepenses(): Collection
    {
        return $this->depenses;
    }

    public function addDepense(DepensesType $depense): self
    {
        if (!$this->depenses->contains($depense)) {
            $this->depenses[] = $depense;
        }

        return $this;
    }

    public function removeDepense(DepensesType $depense): self
    {
        if ($this->depenses->contains($depense)) {
            $this->depenses->removeElement($depense);
        }

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSlug(): ?string
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->title);
    }

    public function getSlugName(): ?string
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->title);
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

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
