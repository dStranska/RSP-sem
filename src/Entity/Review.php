<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 */
class Review
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_article;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_user;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $score;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $approved;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdArticle(): ?int
    {
        return $this->id_article;
    }

    public function setIdArticle(int $id_article): self
    {
        $this->id_article = $id_article;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getApproved(): ?int
    {
        return $this->approved;
    }

    public function setApproved(?int $approved): self
    {
        $this->approved = $approved;

        return $this;
    }
}
