<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\R
 * epository\ArticleFileRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class ArticleFile
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
     * @ORM\Column(type="string", length=255)
     */
    private $full_path;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_last;

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

    public function getFullPath(): ?string
    {
        return $this->full_path;
    }

    public function setFullPath(string $full_path): self
    {
        $this->full_path = $full_path;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->created_date;
    }

    public function setCreatedDate(\DateTimeInterface $created_date): self
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getIsLast(): ?bool
    {
        return $this->is_last;
    }

    public function setIsLast(bool $is_last): self
    {
        $this->is_last = $is_last;

        return $this;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->setCreatedDate(new \DateTime());
    }
}
