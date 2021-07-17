<?php

namespace App\Entity;

use App\Repository\AppTokenRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppTokenRepository::class)
 */
class AppToken implements Token
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=510)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $creationDate = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $expirationDate = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTimeInterface $lastAccessDate = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="appTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $uuid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
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

    public function setExpirationDate(?DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getLastAccessDate(): ?DateTimeInterface
    {
        return $this->lastAccessDate;
    }

    public function setLastAccessDate(?DateTimeInterface $lastAccessDate): self
    {
        $this->lastAccessDate = $lastAccessDate;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * @return Collection|AccessToken[]
     *
     * @psalm-return Collection<array-key, AccessToken>
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }

    /**
     * @return Collection|RefreshToken[]
     *
     * @psalm-return Collection<array-key, RefreshToken>
     */
    public function getRefreshTokens(): Collection
    {
        return $this->refreshTokens;
    }
}
