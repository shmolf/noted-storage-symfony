<?php

namespace App\Entity;

use App\Repository\AppTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppTokenRepository::class)
 */
class AppToken
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
    private $authorizationToken;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastAccessDate;

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

    public function getAuthorizationToken(): ?string
    {
        return $this->authorizationToken;
    }

    public function setAuthorizationToken(string $authorizationToken): self
    {
        $this->authorizationToken = $authorizationToken;

        return $this;
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

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getLastAccessDate(): ?\DateTimeInterface
    {
        return $this->lastAccessDate;
    }

    public function setLastAccessDate(?\DateTimeInterface $lastAccessDate): self
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
}
