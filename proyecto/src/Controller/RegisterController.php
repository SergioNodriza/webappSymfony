<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
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
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $user->setPassword($userPasswordEncoder->encodePassword($user, $form['password']->getData()));
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', User::REGISTER_OK);
                //return $this->redirectToRoute('register');
            } catch (\Exception $e) {
                $this->addFlash('fail', User::REGISTER_FAIL);
            }

        }

        return $this->render('register/register.html.twig', [
            'formRegister' => $form->createView(),
        ]);
    }
}
