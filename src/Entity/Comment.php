<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Post $post = null;

    // Nouvelle relation ManyToOne pour le commentaire parent (si c'est une réponse)
    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'replies')]
    #[ORM\JoinColumn(name: 'parent_comment_id', referencedColumnName: 'id', nullable: true)]
    private ?Comment $parentComment = null;

    // Nouvelle relation OneToMany pour les réponses à ce commentaire
    #[ORM\OneToMany(mappedBy: 'parentComment', targetEntity: Comment::class)]
    private $replies;

    public function __construct()
    {
        $this->replies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
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

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): static
    {
        $this->post = $post;

        return $this;
    }

    // Getter et Setter pour le commentaire parent
    public function getParentComment(): ?self
    {
        return $this->parentComment;
    }

    public function setParentComment(?self $parentComment): static
    {
        $this->parentComment = $parentComment;

        return $this;
    }

    // Getter et Setter pour les réponses
    public function getReplies(): \Doctrine\Common\Collections\Collection
    {
        return $this->replies;
    }

    public function addReply(Comment $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies[] = $reply;
            $reply->setParentComment($this);
        }

        return $this;
    }

    public function removeReply(Comment $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            if ($reply->getParentComment() === $this) {
                $reply->setParentComment(null);
            }
        }

        return $this;
    }
}