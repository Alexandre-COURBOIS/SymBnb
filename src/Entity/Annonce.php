<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnnonceRepository::class)
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(
 *     fields={"title"},message="Une autre annonce possède déjà ce titre, merci de le modifier."
 * )
 */
class Annonce
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10, max="200", minMessage="Le titre doit faire plus de 10 caractères", maxMessage="Le titre doit faire moins de 200 caractères")
     * @Assert\NotBlank(message="merci de renseigner ce champ")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="merci de renseigner ce champ")
     */
    private $price;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=20,minMessage="Votre introduction doit faire plus de 20 caractères")
     * @Assert\NotBlank(message="merci de renseigner ce champ")
     */
    private $introduction;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=100,minMessage="Votre description ne peut pas faire moins de 100 caractères")
     * @Assert\NotBlank(message="merci de renseigner ce champ")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url()
     * @Assert\NotBlank(message="merci de renseigner ce champ")
     */
    private $coverImage;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="merci de renseigner ce champ")
     */
    private $rooms;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifiedAt;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="ad", orphanRemoval=true)
     * @Assert\Valid()
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="annonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="annonce", orphanRemoval=true)
     */
    private $reservations;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="annonce", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Permet d'intialiser le slug et de l'update sur la modification de l'article
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initSlug()
    {

        if (empty($this->slug)) {
            $slug = new Slugify();
            $this->slug = $slug->slugify($this->title);
        } elseif (!empty($this->slug)) {
            $slug = new Slugify();
            $this->slug = $slug->slugify($this->title);
        }

    }

    /**
     * Permet d'obtenir la moyenne générale des notes d'une annonce
     */
    public function getAvgRatings(){
    /*calculer la somme des notations : array_reduce = reduit le tableau à une seule valeur, ici comments est un array collection donc
    on le transforme en vrai tableau, en suite j'initialise la fonction avec deux paramètres, total qui sera l'index des commentaires, et
    les commentaires tiré du tableau comments*/
        $sum = array_reduce($this->comments->toArray(), function($total, $comment) {
            // le total s'incrémente en fonction des notes pour fournir une somme finale
            return $total + $comment->getRating();
            // le total vaut 0 de base
        },0);
        // si il n'y a pas de commentaire je n'execute pas le calcul sinon je divise par zéro et erreur php.
        if (count($this->comments) > 0) return $sum / count($this->comments);
        return 0;
    }

    /**
     * Récupère les jours déjà réservés sur une annonce sous forme de tableau.
     */
    public function getNotAvailableDays()
    {
        // Récupérer les réservations déjà faites boucler dessus et voir les jours déjà pris :

        $notAvailableDays = [];

        // Boucle pour aller chercher chaque reservation déjà faite
        foreach ($this->reservations as $reservation) {
            // Calcul des jours qui se trouvent entre startdate et enddate et rend le résultat en seconde sur une journée dans un tableau
            $resultat = range($reservation->getStartDate()->getTimeStamp(), $reservation->getEndDate()->getTimeStamp(), 24 * 60 * 60);
            // stock toutes mes valeurs de résultat au format Y-m-d dans un tableau
            $days = array_map(function ($dayTimeStamp) {
                return new \DateTime(date('Y-m-d', $dayTimeStamp));
            }, $resultat);

            // Je merge les deux tableaux afin que mon tableau notavailable days contienne les jours déjà reservés
            $notAvailableDays = array_merge($notAvailableDays, $days);
        }
        return $notAvailableDays;
    }

    /**
     * Permet de récupérer le commentaire d'un auteur (s'il existe) par rapport à une annonce.
     */
    public function getCommentFromAuthor(User $author) {
        foreach ($this->comments as $comment) {
            if($comment->getAuthor() === $author) return $comment;
        }
    }

    /**
     * Permet de set l'heure à laquelle l'article est créer
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initCreateDate()
    {

        if (empty($this->getCreatedAt())) {
            $this->setCreatedAt(new \DateTime());
        }

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): self
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): self
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getModifiedAt(): ?\DateTimeInterface
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?\DateTimeInterface $modifiedAt): self
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

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
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setAnnonce($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getAnnonce() === $this) {
                $reservation->setAnnonce(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAnnonce($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAnnonce() === $this) {
                $comment->setAnnonce(null);
            }
        }

        return $this;
    }
}
