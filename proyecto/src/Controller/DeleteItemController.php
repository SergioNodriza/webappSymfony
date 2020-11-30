<?php

namespace App\Controller;

use App\Entity\Item;
use App\Model\DeleteItem;
use App\Model\ListItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteItemController extends AbstractController
{
    private ListItem $listItem;
    private DeleteItem $deleteItem;

    public function __construct(ListItem $listItem, DeleteItem $deleteItem)
    {
        $this->listItem = $listItem;
        $this->deleteItem = $deleteItem;
    }

    /**
     * @Route("/delete", name="delete-item")
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {

        if ($request->isMethod('POST')) {

            $id = $_POST['item'];
            return $this->redirect("/delete-item/" . $id);
        }

        $user = $this->getUser();
        $items = $this->listItem->listItemByUser($user);

        return $this->render('item/choose-item.html.twig', [
            'items' => $items,
            'action' => 'delete'
        ]);
    }

    /**
     * @Route("/delete-item/{id}", name="delete-item-id")
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function deleteItem(string $id, Request $request)
    {
        $user = $this->getUser();
        $item = $this->listItem->listItemByUserAndId($user, $id);

        if ($item == null) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        if ($request->isMethod('POST')) {

            $result = $this->deleteItem->deleteItem($item);

            if ($result) {
                $this->addFlash('success', Item::ITEM_DELETED);
                return $this->redirectToRoute('delete-item');
            } else {
                $this->addFlash('fail', Item::ITEM_FAIL);
                return $this->redirectToRoute('delete-item-id');
            }
        }

        return $this->render('item/delete-item.html.twig', [
            'item' => $item
        ]);
    }
}
