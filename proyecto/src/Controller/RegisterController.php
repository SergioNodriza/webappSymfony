<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Model\FlashMessage;
use App\Model\Register;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterController extends AbstractController
{

    private Register $register;
    private TranslatorInterface $translator;

    public function __construct(Register $register, TranslatorInterface $translator)
    {
        $this->register = $register;
        $this->translator = $translator;
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
            $result = $this->register->register($user);
            $name = $user->getUsername();

            if ($result) {
                $message = $this->translator->trans(FlashMessage::REGISTER_OK);
                $this->addFlash('success', $message);
                return $this->redirectToRoute('app_login', ['new' => $name]);
            } else {

                $message = $this->translator->trans(FlashMessage::REGISTER_FAIL);
                $this->addFlash('fail', $message);
                return $this->redirectToRoute("register");
            }
        }

        return $this->render('register/register.html.twig', [
            'formRegister' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/register/activate/{id}", name="activateRegister")
     * @param Request $request
     * @param string $id
     * @param UserRepository $userRepository
     * @return Response
     */
    public function activate(Request $request, string $id, UserRepository $userRepository) {

        $accepted = $request->query->getBoolean('activate');
        $user = $userRepository->findOneBy(['id' => $id]);

        if ($user == null) {
            return new Response("User not found");
        }

        $result = $this->register->activate($accepted, $user);
        return new Response($result);
    }
}
