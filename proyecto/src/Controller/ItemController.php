<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ItemController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @Route("/", name="inicio")
     * @param ItemRepository $itemRepository
     * @return Response
     */
    public function list(ItemRepository $itemRepository): Response
    {
        $user = $this->getUser();
        $items = $itemRepository->findBy(['user' => $user]);

        return $this->render('dashboard/dashboard.html.twig', [
            'items' => $items,
        ]);
    }

    /**
     * @Route("/new-item", name="new-item")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
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
                return $this->redirectToRoute("new-item");
            } catch (\Exception $e) {
                $this->addFlash('fail', Item::ITEM_FAIL);
                return $this->redirectToRoute("new-item");
            }
        }

        return $this->render('item/new-item.html.twig', [

            'formNewItem' => $form->createView(),
        ]);
    }

    /**
     * @Route("/update-item", name="update-item")
     * @param Request $request
     * @param ItemRepository $itemRepository
     * @return Response
     */
    public function update(Request $request, ItemRepository $itemRepository): Response
    {

        if ($request->isMethod('POST')) {

            $id = $_POST['item'];

            return $this->redirect("/update-item/" . $id);

        } else {

            $user = $this->getUser();
            $items = $itemRepository->findBy(['user' => $user]);

            return $this->render('item/choose-item.html.twig', [
                'items' => $items,
                'action' => 'update'
            ]);
        }
    }

    /**
     * @Route("/update-item/{id}", name="update-item-id")
     * @param string $id
     * @param ItemRepository $itemRepository
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function updateItem(string $id, ItemRepository $itemRepository, Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $item = $itemRepository->findOneBy(["id" => $id, "user" => $user]);

        if ($item == null) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $formUpdate = $this->createForm(ItemType::class, $item);
        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {

            try {

                $entityManager->persist($item);
                $entityManager->flush();
                $this->addFlash('success', Item::ITEM_UPDATED);
                return $this->redirect("/update-item/" . $id);
            } catch (\Exception $e) {
                $this->addFlash('fail', Item::ITEM_FAIL);
                return $this->redirect("/update-item/" . $id);
            }
        }

        return $this->render('item/update-item.html.twig', [
            'item' => $item,
            'formUpdate' => $formUpdate->createView()
        ]);
    }

    /**
     * @Route("/delete", name="delete-item")
     * @param Request $request
     * @param ItemRepository $itemRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, ItemRepository $itemRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $items = $itemRepository->findBy(['user' => $user]);

        if ($request->isMethod('POST')) {

            $id = $_POST['item'];
            $item = $itemRepository->findOneBy(["id" => $id, "user" => $user]);

            try {
                $entityManager->remove($item);
                $entityManager->flush();
                $this->addFlash('success', Item::ITEM_DELETED);
                return $this->redirectToRoute('delete-item');
            } catch (\Exception $exception) {
                $this->addFlash('fail', Item::ITEM_FAIL);
                return $this->redirectToRoute('delete-item');
            }
        }

        return $this->render('item/choose-item.html.twig', [
            'items' => $items,
            'action' => 'delete'
        ]);
    }
}
