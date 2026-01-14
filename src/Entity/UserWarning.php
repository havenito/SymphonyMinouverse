<?php

namespace App\Entity;

use App\Repository\UserWarningRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserWarningRepository::class)]
class UserWarning
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $issuedBy = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reason = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $issuedAt = null;

    #[ORM\ManyToOne(targetEntity: CommentReport::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?CommentReport $relatedReport = null;

    public function __construct()
    {
        $this->issuedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getIssuedBy(): ?User
    {
        return $this->issuedBy;
    }

    public function setIssuedBy(?User $issuedBy): static
    {
        $this->issuedBy = $issuedBy;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    public function getIssuedAt(): ?\DateTimeInterface
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeInterface $issuedAt): static
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }

    public function getRelatedReport(): ?CommentReport
    {
        return $this->relatedReport;
    }

    public function setRelatedReport(?CommentReport $relatedReport): static
    {
        $this->relatedReport = $relatedReport;
        return $this;
    }
}