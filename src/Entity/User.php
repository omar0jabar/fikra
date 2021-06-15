<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EgioDigital\CMSBundle\Entity\ArticleLike;
use App\Entity\Company;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"})
 * @Vich\Uploadable
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"approved_projects_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     * @Groups({"approved_projects_read"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="50")
     * @Groups({"approved_projects_read"})
     */
    private $lastName;

   /**
    * @ORM\Column(type="string", length=255, nullable=true)
    * @Assert\Length(min=9, max=9)
    */
   private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Groups({"approved_projects_read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=8, max="100")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @var File|null
     * @Assert\Image(
     *      mimeTypes={"image/jpeg", "image/png"},
     *    maxSize="3M"
     * )
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="avatar")
     */
    private $imageFile;

   /**
    * @ORM\Column(type="string", length=255)
    * @Assert\Expression(
    *     "this.getProfile() in ['admin', 'startuper', 'investor', 'bearer-of-an-associative-project']",
    * )
    */
   private $profile;

   /**
    * @ORM\ManyToOne(targetEntity="App\Entity\Role", inversedBy="users")
    * @ORM\JoinColumn(nullable=false)
    * @Groups({"approved_projects_read"})
    */
   private $role;

   /**
    * @ORM\Column(type="datetime", nullable=true)
    */
   private $lastLogin;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateToken;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBanned;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="author")
     */
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApprovedProject", mappedBy="author", orphanRemoval=true)
     */
    private $approvedProjects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Faq", mappedBy="author")
     */
    private $faqs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="author")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="EgioDigital\CMSBundle\Entity\ArticleLike", mappedBy="user",cascade={"persist"})
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MailSended", mappedBy="user")
     */
    private $mailSendeds;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RequestDocumentation", mappedBy="user")
     */
    private $requestDocumentations;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ProjectLike", mappedBy="user")
     */
    private $projectLikes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageResponse", mappedBy="user")
     */
    private $messageResponses;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $socialReason;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Company", mappedBy="user")
     */
    private $companies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompanyComment", mappedBy="author")
     */
    private $companyComments;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompanyCommentResponse", mappedBy="user")
     */
    private $companyCommentResponses;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CompanyLike", mappedBy="user")
     */
    private $companyLikes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="users")
     */
    private $city;

   /**
    * User constructor.
    * @throws \Exception
    */
    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->projects = new ArrayCollection();
        $this->approvedProjects = new ArrayCollection();
        $this->faqs = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->mailSendeds = new ArrayCollection();
        $this->requestDocumentations = new ArrayCollection();
        $this->projectLikes = new ArrayCollection();
        $this->messageResponses = new ArrayCollection();
        $this->companies = new ArrayCollection();
        $this->companyComments = new ArrayCollection();
        $this->companyCommentResponse = new ArrayCollection();
    }

   /**
    * @ORM\PrePersist()
    * @throws \Exception
    */
    public function initializeOnPrePersist(): void
    {
        $this->setIsBanned(false);
        $this->setIsDeleted(false);
        $dateTimeNow = new \DateTime('now');
        $this->setCreatedAt($dateTimeNow);
    }

    public function file_existes($avatar) {
        return file_exists(substr($avatar, 1));
    }

   /**
    * @ORM\PreUpdate
    * @throws \Exception
    */
    public function updatedTimestamps(): void
    {
        $dateTimeNow = new \DateTime('now');
        $this->setUpdatedAt($dateTimeNow);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->firstName . " " . $this->lastName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

   public function getPhone(): ?string
   {
      return $this->phone;
   }

   public function setPhone(string $phone): self
   {
      $this->phone = $phone;

      return $this;
   }

    public function __toString(): ?string
    {
        return (string)$this->email;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

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
     * @return User
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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getDateToken(): ?\DateTimeInterface
    {
        return $this->dateToken;
    }

    public function setDateToken(?\DateTimeInterface $dateToken): self
    {
        $this->dateToken = $dateToken;

        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

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

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRoles()
    {
        $roles[] = $this->getRole()->getLabel();
        return $roles;
    }

    public function getSalt() {}

    public function getUsername()
    {
        return $this->getEmail();
    }

    public function eraseCredentials() {}

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->email,
            $this->password,
            $this->isActive,
            $this->isBanned
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->isActive,
            $this->isBanned
            ) = unserialize($serialized, ['allowed_classes' => false]);
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setAuthor($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getAuthor() === $this) {
                $project->setAuthor(null);
            }
        }

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * @return Collection|ApprovedProject[]
     */
    public function getApprovedProjects(): Collection
    {
        return $this->approvedProjects;
    }

    public function addApprovedProject(ApprovedProject $approvedProject): self
    {
        if (!$this->approvedProjects->contains($approvedProject)) {
            $this->approvedProjects[] = $approvedProject;
            $approvedProject->setAuthor($this);
        }

        return $this;
    }

    public function removeApprovedProject(ApprovedProject $approvedProject): self
    {
        if ($this->approvedProjects->contains($approvedProject)) {
            $this->approvedProjects->removeElement($approvedProject);
            // set the owning side to null (unless already changed)
            if ($approvedProject->getAuthor() === $this) {
                $approvedProject->setAuthor(null);
            }
        }

        return $this;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(string $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection|Faq[]
     */
    public function getFaqs(): Collection
    {
        return $this->faqs;
    }

    public function addFaq(Faq $faq): self
    {
        if (!$this->faqs->contains($faq)) {
            $this->faqs[] = $faq;
            $faq->setAuthor($this);
        }

        return $this;
    }

    public function removeFaq(Faq $faq): self
    {
        if ($this->faqs->contains($faq)) {
            $this->faqs->removeElement($faq);
            // set the owning side to null (unless already changed)
            if ($faq->getAuthor() === $this) {
                $faq->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setAuthor($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getAuthor() === $this) {
                $message->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ArticleLike[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(ArticleLike $articleLike): self
    {
        if (!$this->likes->contains($articleLike)) {
            $this->likes[] = $articleLike;
            $articleLike->setUser($this);
        }

        return $this;
    }

    public function removeLike(ArticleLike $articleLike): self
    {
        if ($this->likes->contains($articleLike)) {
            $this->likes->removeElement($articleLike);
            // set the owning side to null (unless already changed)
            if ($articleLike->getUser() === $this) {
                $articleLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MailSended[]
     */
    public function getMailSendeds(): Collection
    {
        return $this->mailSendeds;
    }

    public function addMailSended(MailSended $mailSended): self
    {
        if (!$this->mailSendeds->contains($mailSended)) {
            $this->mailSendeds[] = $mailSended;
            $mailSended->setUser($this);
        }

        return $this;
    }

    public function removeMailSended(MailSended $mailSended): self
    {
        if ($this->mailSendeds->contains($mailSended)) {
            $this->mailSendeds->removeElement($mailSended);
            // set the owning side to null (unless already changed)
            if ($mailSended->getUser() === $this) {
                $mailSended->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RequestDocumentation[]
     */
    public function getRequestDocumentations(): Collection
    {
        return $this->requestDocumentations;
    }

    public function addRequestDocumentation(RequestDocumentation $requestDocumentation): self
    {
        if (!$this->requestDocumentations->contains($requestDocumentation)) {
            $this->requestDocumentations[] = $requestDocumentation;
            $requestDocumentation->setUser($this);
        }

        return $this;
    }

    public function removeRequestDocumentation(RequestDocumentation $requestDocumentation): self
    {
        if ($this->requestDocumentations->contains($requestDocumentation)) {
            $this->requestDocumentations->removeElement($requestDocumentation);
            // set the owning side to null (unless already changed)
            if ($requestDocumentation->getUser() === $this) {
                $requestDocumentation->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ProjectLike[]
     */
    public function getProjectLikes(): Collection
    {
        return $this->projectLikes;
    }

    public function addProjectLike(ProjectLike $projectLike): self
    {
        if (!$this->projectLikes->contains($projectLike)) {
            $this->projectLikes[] = $projectLike;
            $projectLike->setUser($this);
        }

        return $this;
    }

    public function removeProjectLike(ProjectLike $projectLike): self
    {
        if ($this->projectLikes->contains($projectLike)) {
            $this->projectLikes->removeElement($projectLike);
            // set the owning side to null (unless already changed)
            if ($projectLike->getUser() === $this) {
                $projectLike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MessageResponse[]
     */
    public function getMessageResponses(): Collection
    {
        return $this->messageResponses;
    }

    public function addMessageResponse(MessageResponse $messageResponse): self
    {
        if (!$this->messageResponses->contains($messageResponse)) {
            $this->messageResponses[] = $messageResponse;
            $messageResponse->setUser($this);
        }

        return $this;
    }

    public function removeMessageResponse(MessageResponse $messageResponse): self
    {
        if ($this->messageResponses->contains($messageResponse)) {
            $this->messageResponses->removeElement($messageResponse);
            // set the owning side to null (unless already changed)
            if ($messageResponse->getUser() === $this) {
                $messageResponse->setUser(null);
            }
        }

        return $this;
    }

    public function getSocialReason(): ?string
    {
        return $this->socialReason;
    }

    public function setSocialReason(?string $socialReason): self
    {
        $this->socialReason = $socialReason;

        return $this;
    }

    /**
     * @return Collection|Company[]
     */
    public function getCompanies(): Collection
    {
        return $this->companies;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->companies->contains($company)) {
            $this->companies[] = $company;
            $company->setUser($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->companies->contains($company)) {
            $this->companies->removeElement($company);
            // set the owning side to null (unless already changed)
            if ($company->getUser() === $this) {
                $company->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CompanyComment[]
     */
    public function getCompanyComments(): Collection
    {
        return $this->companyComments;
    }

    public function addCompanyComment(CompanyComment $companyComment): self
    {
        if (!$this->companyComments->contains($companyComment)) {
            $this->companyComments[] = $companyComment;
            $companyComment->setAuthor($this);
        }

        return $this;
    }

    public function removeCompanyComment(CompanyComment $companyComment): self
    {
        if ($this->companyComments->contains($companyComment)) {
            $this->companyComments->removeElement($companyComment);
            // set the owning side to null (unless already changed)
            if ($companyComment->getAuthor() === $this) {
                $companyComment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CompanyCommentResponse[]
     */
    public function getCompanyCommentResponses(): Collection
    {
        return $this->companyCommentResponses;
    }

    public function addCompanyCommentResponse(CompanyCommentResponse $companyCommentResponse): self
    {
        if (!$this->companyCommentResponses->contains($companyCommentResponse)) {
            $this->companyCommentResponses[] = $companyCommentResponse;
            $companyCommentResponse->setUser($this);
        }

        return $this;
    }

    public function removeCompanyCommentResponse(CompanyCommentResponse $companyCommentResponse): self
    {
        if ($this->companyCommentResponses->contains($companyCommentResponse)) {
            $this->companyCommentResponses->removeElement($companyCommentResponse);
            // set the owning side to null (unless already changed)
            if ($companyCommentResponse->getUser() === $this) {
                $companyCommentResponse->setUser(null);
            }
        }

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }
}
