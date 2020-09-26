<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Reservation;
use App\Form\ReservationType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

        if($form->isSubmitted() && $form->isValid()) {

            $reservation->setBooker($this->getUser())
                         ->setAnnonce($annonce);

            $manager->persist($reservation);
            $manager->flush();

            return $this->redirectToRoute('reservation_success', ['id' => $reservation->getId()]);
        }

        return $this->render('reservation/reservation.html.twig', [
            'annonce' => $annonce,
            'form' => $form->createView()
        ]);
    }
}
