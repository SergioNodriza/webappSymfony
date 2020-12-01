<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Model\FlashMessage;
use App\Model\Register;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    private Register $register;

    public function __construct(Register $register)
    {
        $this->register = $register;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        if ($this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("app_login");
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($userPasswordEncoder->encodePassword($user, $form['password']->getData()));
            $name = $user->getName();
            $result = $this->register->register($user);

            if ($result) {
                $this->addFlash('success', FlashMessage::REGISTER_OK);
                return $this->redirect('/{_locale}/login/' . $name);
            } else {
                $this->addFlash('fail', FlashMessage::REGISTER_FAIL);
                return $this->redirectToRoute("register");
            }
        }

        return $this->render('register/register.html.twig', [
            'formRegister' => $form->createView(),
        ]);
    }
}
