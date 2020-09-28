<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Comment;
use App\Entity\Reservation;
use App\Form\CommentType;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    /**
     * Création du formulaire de réservation
     *
     * @Route("/annonces/{slug}/reservation", name="reservation_create")
     *
     * @IsGranted("ROLE_USER")
     */
    public function reservation(Annonce $annonce, Request $request, EntityManagerInterface $manager)
    {
        $reservation = new Reservation();

        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reservation->setBooker($this->getUser())
                ->setAnnonce($annonce);

            // Si les dates ne sont pas disponibles -> erreur
            if (!$reservation->isBookableDates()) {

                $this->addFlash('warning', 'Les dates que vous avez choisis sont déjà reservées.');

            } else {

                $manager->persist($reservation);
                $manager->flush();

                return $this->redirectToRoute('reservation_show', ['id' => $reservation->getId(), 'success' => true]);
            }
        }

        return $this->render('reservation/reservation.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }


    /**
     * Affiche la page d'une reservation
     *
     * @Route ("/reservation/{id}", name="reservation_show")
     *
     * @Security ("is_granted('ROLE_USER') and user == reservation.getBooker()", message="Vous n'avez pas le droit d'accèder à cette page.")
     */
    public function show(Reservation $reservation, Request $request, EntityManagerInterface $manager)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAnnonce($reservation->getAnnonce())
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success','Votre commentaire à bien été ajouté !');

        }


        return $this->render('reservation/show.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);
    }

}
