<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    /**
     * Direction vers la page des annonces
     *
     * @Route("/annonces", name="annonce_index")
     */
    public function index(AnnonceRepository $repository)
    {

//        Méthode sous symfony 4.4:
//        $repo = $this->getDoctrine()->getRepository(Annonce::class);

        $annonces = $repository->findAll();

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/annonces/{slug}", name="annonce_show")
     *
     * @return Response
     */
    public function show(Annonce $annonce)
    {

        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
        ]);
    }




}
