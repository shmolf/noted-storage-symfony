<?php

namespace App\Entity;

use App\Repository\NoteTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=NoteTagRepository::class)
 */
class NoteTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("main")
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="noteTags")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToMany(targetEntity=MarkdownNote::class, mappedBy="tags")
     */
    private Collection $markdownNotes;

    public function __construct()
    {
        $this->markdownNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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
            $markdownNote->addTag($this);
        }

        return $this;
    }

    public function removeMarkdownNote(MarkdownNote $markdownNote): self
    {
        if ($this->markdownNotes->removeElement($markdownNote)) {
            $markdownNote->removeTag($this);
        }

        return $this;
    }
}
