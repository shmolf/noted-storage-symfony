<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RefreshTokenRepository::class)
 */
class RefreshToken implements Token
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $creationDate = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $expirationDate = null;

    /**
     * @ORM\Column(type="string", length=510)
     */
    private string $token;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="refreshTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
