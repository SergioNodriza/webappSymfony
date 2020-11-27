<?php

namespace App\Controller;

use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashBoardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @Route("/", name="inicio")
     * @param ItemRepository $itemRepository
     * @return Response
     */
    public function index(ItemRepository $itemRepository): Response
    {
        $user = $this->getUser();
        $items = $itemRepository->findBy(['user' => $user]);

        return $this->render('dashboard/dashboard.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @Route("/headerLogged", name="headerLogged")
     * @return Response
     */
    public function header(): Response
    {
        return $this->render('header/headerLogged.html.twig', [
        ]);
    }
    /**
     * @Route("/header", name="header")
     * @return Response
     */
    public function headerLogged(): Response
    {
        return $this->render('header/header.html.twig', [
        ]);
    }
}