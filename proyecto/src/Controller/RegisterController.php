<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        //$this->denyAccessUnlessGranted('IS_ANONYMOUS');

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $user->setPassword($userPasswordEncoder->encodePassword($user, $form['password']->getData()));
                $name = $user->getName();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', User::REGISTER_OK);
                return $this->redirect('/login/' . $name);
            } catch (Exception $e) {
                $this->addFlash('fail', User::REGISTER_FAIL);
                return $this->redirectToRoute("register");
            }
        }

        return $this->render('register/register.html.twig', [
            'formRegister' => $form->createView(),
        ]);
    }
}
