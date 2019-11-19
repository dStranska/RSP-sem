<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ArticleRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Article
{

    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRES = 'in_progress';
    const STATUS_RE_DONE = 're_done';
    const STATUS_REPAIR = 'repair';
    const STATUS_SUCCESS = 'success';
    const STATUS_DECLINE = 'decline';

    const ROLE_AUTOR = 'autor';
    const ROLE_REDACTOR = 'redaktor';
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
     * @ORM\Column(type="integer")
     */
    private $id_autor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $autors_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status = self::STATUS_NEW;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_journal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $id_theme;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $theme;

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @param mixed $theme
     */
    public function setTheme($theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return mixed
     */
    public function getIdTheme()
    {
        return $this->id_theme;
    }

    /**
     * @param mixed $id_theme
     */
    public function setIdTheme($id_theme): void
    {
        $this->id_theme = $id_theme;
    }

    public $file;

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

    public function getIdAutor(): ?int
    {
        return $this->id_autor;
    }

    public function setIdAutor(int $id_autor): self
    {
        $this->id_autor = $id_autor;

        return $this;
    }

    public function getAutorsName(): ?string
    {
        return $this->autors_name;
    }

    public function setAutorsName(string $autors_name): self
    {
        $this->autors_name = $autors_name;

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

    public function getIdJournal(): ?int
    {
        return $this->id_journal;
    }

    public function setIdJournal(?int $id_journal): self
    {
        $this->id_journal = $id_journal;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->setCreatedDate(new \DateTime());
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

    public function getActualStatus()
    {
        switch ($this->getStatus()) {
            case self::STATUS_NEW:
                return 'Nový';
                break;
            case self::STATUS_IN_PROGRES:
                return "Čeká se na recenzi";
                break;
            case self::STATUS_RE_DONE:
                return "Recenze hotová, čeká se na vyjádření redaktora";
                break;
            case self::STATUS_REPAIR:
                return "Recenze k dispozici";
                break;
            case self::STATUS_SUCCESS:
                return "Článek byl schválen a zařazen do čísla XY";
                break;
            case self::STATUS_DECLINE:
                return "Článek byl zamítnut";
                break;
        }
    }
}
