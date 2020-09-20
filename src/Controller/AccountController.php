<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     *
     * Permet de se connecter.
     *
     * @Route("/login", name="account_login")
     *
     * @return Response
     *
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     *Permet de se deconnecter
     *
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout()
    {

    }

    /**
     *Permet d'afficher le formulaire d'inscription
     *
     * @Route("/register", name="account_regiser")
     *
     * @return Response
     */
    public function register()
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        return  $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
