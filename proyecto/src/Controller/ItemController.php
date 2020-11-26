<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    /**
     * @Route("/new-item", name="new-item")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $user = $this->getUser();
                $item->setUser($user);
                $entityManager->persist($item);
                $entityManager->flush();
                $this->addFlash('success', Item::ITEM_OK);
            } catch (\Exception $e) {
                $this->addFlash('fail', Item::ITEM_FAIL);
            }
        }

        return $this->render('item/new-item.html.twig', [
            'controller_name' => 'ItemController',
            'formNewItem' => $form->createView(),
        ]);
    }
}
