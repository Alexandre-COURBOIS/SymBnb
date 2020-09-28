<?php

namespace App\Controller;

use App\Repository\AnnonceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminAnnonceController extends AbstractController
{
    /**
     * @Route("/admin/annonces", name="admin_annonces_index")
     */
    public function index(AnnonceRepository $repository)
    {
        return $this->render('admin/annonce/index.html.twig', [
            'annonces' => $repository->findAll()
        ]);
    }
}
