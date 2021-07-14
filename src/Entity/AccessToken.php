<?php

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AccessTokenRepository::class)
 */
class AccessToken
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
    private DateTimeInterface $creationDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $expirationDate;

    /**
     * @ORM\ManyToOne(targetEntity=AppToken::class, inversedBy="accessTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?AppToken $appToken = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $token;

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

    public function getAppToken(): ?AppToken
    {
        return $this->appToken;
    }

    public function setAppToken(?AppToken $appToken): self
    {
        $this->appToken = $appToken;

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
}