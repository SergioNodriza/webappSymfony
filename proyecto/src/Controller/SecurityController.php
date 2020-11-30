<?php

namespace App\Controller;

use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login/{new}", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param string|null $new
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, $new = null): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();

        if ($new == null) {
            $lastUsername = $authenticationUtils->getLastUsername();
        } else {
            $lastUsername = $new;
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
