<?php

namespace App\Entity;

use App\Repository\NoteTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="noteTags")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userId;

    /**
     * @ORM\ManyToMany(targetEntity=MarkdownNote::class, mappedBy="tags")
     */
    private $markdownNotes;

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
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return Collection|MarkdownNote[]
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
