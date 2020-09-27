<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity=Annonce::class, inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $annonce;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThan("Today", message="La date d'arrivée doit-être ultérieur à ce jour")
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit être ultérieur à la date d'arrivée")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $commentaire;

    /**
     * permet de set le created at
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initCreatedAt(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }
    }

    /**
     * permet de set l'amount
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function initAmount(){
        if(empty($this->amount)){
            $this->amount = $this->annonce->getPrice() * $this->getDuration();
        }
    }

    /**
     * Calcul de la durée du séjour pour pouvoir le multiplier par la somme afin de définir l'amount
     */
    public function getDuration() {
        $marge = $this->endDate->diff($this->startDate);
        return $marge->days;
    }

    /**
     * Permet de savoir si les dates sont disponibles
     */
    public function isBookableDates() {
        // connaitre les dates déjà reservées
        $notAvailableDays = $this->annonce->getNotAvailableDays();
        // comparer les dates choisies avec les dates déjà reservées
        $bookingDays = $this->getDays();

        $format = function ($day){
            return $day->format('Y-m-d');
        };

        // Tableau qui contient ma demande de réservation sous forme de strings au format Y-m-d
        $days = array_map($format, $bookingDays);

        // Tableau qui contient mes jours qui ne peuvent pas être réservés au format Y-m-D
        $notAvailable = array_map($format, $notAvailableDays);

        foreach ($days as $day) {
            if(array_search($day, $notAvailable) !== false) return false;
        }
        return true;
    }

    /**
     * Permet de récupérer un tableau qui correspond aux journées de la reservation en question
     */
    public function getDays() {
        //Retourne le temps en seconde entre la date de départ et de fin
        $resultat = range($this->startDate->getTimeStamp(), $this->endDate->getTimeStamp(), 24*60*60);

        // Retourne tout les jours stocké dans résultats sous forme de tableau au format Y-m-d
        $days = array_map(function($dayTimeStamp){
            return new \DateTime(date('Y-m-d', $dayTimeStamp));
        }, $resultat);


        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAnnonce(): ?Annonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonce $annonce): self
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

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

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }
}
