<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\OneToMany(targetEntity=AppToken::class, mappedBy="user", orphanRemoval=true)
     */
    private $appTokens;

    /**
     * @ORM\OneToMany(targetEntity=MarkdownNote::class, mappedBy="userId", orphanRemoval=true)
     */
    private $markdownNotes;

    /**
     * @ORM\OneToMany(targetEntity=NoteTag::class, mappedBy="userId", orphanRemoval=true)
     */
    private $noteTags;

    public function __construct()
    {
        $this->appTokens = new ArrayCollection();
        $this->markdownNotes = new ArrayCollection();
        $this->noteTags = new ArrayCollection();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     *
     * @return void
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * @return Collection|AppToken[]
     *
     * @psalm-return Collection<array-key, AppToken>
     */
    public function getAppTokens(): Collection
    {
        return $this->appTokens;
    }

    public function addAppToken(AppToken $appToken): self
    {
        if (!$this->appTokens->contains($appToken)) {
            $this->appTokens[] = $appToken;
            $appToken->setUser($this);
        }

        return $this;
    }

    public function removeAppToken(AppToken $appToken): self
    {
        if ($this->appTokens->removeElement($appToken)) {
            // set the owning side to null (unless already changed)
            if ($appToken->getUser() === $this) {
                $appToken->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|MarkdownNote[]
     *
     * @psalm-return Collection<array-key, MarkdownNote>
     */
    public function getMarkdownNotes(): Collection
    {
        return $this->markdownNotes;
    }

    public function addMarkdownNote(MarkdownNote $markdownNote): self
    {
        if (!$this->markdownNotes->contains($markdownNote)) {
            $this->markdownNotes[] = $markdownNote;
            $markdownNote->setUser($this->id);
        }

        return $this;
    }

    public function removeMarkdownNote(MarkdownNote $markdownNote): self
    {
        $this->markdownNotes->removeElement($markdownNote);

        return $this;
    }

    /**
     * @return Collection|NoteTag[]
     *
     * @psalm-return Collection<array-key, NoteTag>
     */
    public function getNoteTags(): Collection
    {
        return $this->noteTags;
    }

    public function addNoteTag(NoteTag $noteTag): self
    {
        if (!$this->noteTags->contains($noteTag)) {
            $this->noteTags[] = $noteTag;
            $noteTag->setUserId($this);
        }

        return $this;
    }

    public function removeNoteTag(NoteTag $noteTag): self
    {
        $this->noteTags->removeElement($noteTag);

        return $this;
    }
}
