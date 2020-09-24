<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Image;
use App\Entity\User;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * Permet de créer une annonce
     *
     * @Route("/annonces/new", name="annonces_create")
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {

        $annonce = new Annonce();

        $form = $this->createForm(AnnonceType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($annonce->getImages() as $image ) {
                $image->setAd($annonce);
                $manager->persist($image);
            }

            $annonce->setAuthor($this->getUser());

            $manager->persist($annonce);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre annonce <strong>' . $annonce->getTitle() . '</strong> a bien été enregistrée !'
            );

            return $this->redirectToRoute('annonce_show', [
                'slug' => $annonce->getSlug()
            ]);

        }

        return $this->render('annonce/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route("/annonces/{slug}/edit", name="annonce_edit")
     *
     * @return Response
     */
    public function edit(Annonce $annonce, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AnnonceType::class, $annonce);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($annonce->getImages() as $image ) {
                $image->setAd($annonce);
                $manager->persist($image);
            }

            $annonce->setModifiedAt(new \DateTime());

            $manager->persist($annonce);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les modifications de l\'annonce <strong>' . $annonce->getTitle() . '</strong> ont bien été modifiées !'
            );

            return $this->redirectToRoute('annonce_show', [
                'slug' => $annonce->getSlug()
            ]);

        }

        return $this->render('annonce/edit.html.twig', [
            'form' => $form->createView(),
            'annonce' => $annonce
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

        $user = $annonce->getAuthor();

        return $this->render('annonce/show.html.twig', [
            'annonce' => $annonce,
            'user' => $user
        ]);
    }


}
