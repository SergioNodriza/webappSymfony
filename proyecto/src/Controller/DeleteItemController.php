<?php

namespace App\Controller;

use App\Entity\Item;
use App\Model\DeleteItem;
use App\Model\ListItem;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param ItemRepository $itemRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(Request $request, ItemRepository $itemRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $items = $this->listItem->listItemByUser($user);

        if ($request->isMethod('POST')) {

            $id = $_POST['item'];
            $item = $this->listItem->listItemByUserAndId($user, $id);

            $result = $this->deleteItem->deleteItem($item);

            if ($result) {
                $this->addFlash('success', Item::ITEM_DELETED);
                return $this->redirectToRoute('delete-item');
            } else {
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
