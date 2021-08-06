<?php

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccessTokenRepository::class)
 */
class AccessToken implements Token
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
    private ?string $token = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="accessTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user = null;

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
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
