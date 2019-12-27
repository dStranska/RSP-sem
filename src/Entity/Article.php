<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_REVIEW_DONE = 'review_done';
    const STATUS_REPAIR = 'repair';
    const STATUS_REPAIR_DONE = 'repair_done';
    const STATUS_ARCHIVED = 'archived';

    const STATUS_DONE = 'done';
    const STATUS_DECLINE = 'decline';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $authors_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_NEW;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticleFile", mappedBy="article", orphanRemoval=true)
     */
    private $articleFiles;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ArticleTheme", inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $theme;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", cascade={"merge"}, inversedBy="article")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Review", mappedBy="article", orphanRemoval=true)
     */
    private $reviews;

    public $file;

    public function __construct()
    {
        $this->articleFiles = new ArrayCollection();
        $this->reviews = new ArrayCollection();
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

    public function getAuthorsName(): ?string
    {
        return $this->authors_name;
    }

    public function setAuthorsName(string $authors_name): self
    {
        $this->authors_name = $authors_name;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

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

    /**
     * @return Collection|ArticleFile[]
     */
    public function getArticleFiles(): Collection
    {
        return $this->articleFiles;
    }

    public function addArticleFile(ArticleFile $articleFile): self
    {
        if (!$this->articleFiles->contains($articleFile)) {
            $this->articleFiles[] = $articleFile;
            $articleFile->setArticle($this);
        }

        return $this;
    }

    public function removeArticleFile(ArticleFile $articleFile): self
    {
        if ($this->articleFiles->contains($articleFile)) {
            $this->articleFiles->removeElement($articleFile);
            // set the owning side to null (unless already changed)
            if ($articleFile->getArticle() === $this) {
                $articleFile->setArticle(null);
            }
        }

        return $this;
    }

    public function getTheme(): ?ArticleTheme
    {
        return $this->theme;
    }

    public function getThemeName(): ?string
    {
        return $this->getTheme()->getName();
    }

    public function setTheme(?ArticleTheme $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->setCreatedDate(new \DateTime());
    }


    public function getActualStatus()
    {
        switch ($this->getStatus()) {
            case self::STATUS_NEW:
                return 'nový';
                break;
            case self::STATUS_IN_PROGRESS:
                return 'čeká se na recenzi';
                break;
            case self::STATUS_REVIEW_DONE:
                return 'recenze hotová';
                break;
            case self::STATUS_REPAIR:
                return 'požadavek na úpravu';
                break;
            case self::STATUS_DONE:
                return 'Schválen';
                break;
            case self::STATUS_DECLINE:
                return 'Zamítnut';
                break;

        }
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setArticle($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->contains($review)) {
            $this->reviews->removeElement($review);
            // set the owning side to null (unless already changed)
            if ($review->getArticle() === $this) {
                $review->setArticle(null);
            }
        }

        return $this;
    }
}
