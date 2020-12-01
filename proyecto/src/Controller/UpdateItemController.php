<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Model\ListItem;
use App\Model\UpdateItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateItemController extends AbstractController
{
    private UpdateItem $updateItem;
    private ListItem $listItem;

    public function __construct(ListItem $listItem, UpdateItem $updateItem)
    {
        $this->updateItem = $updateItem;
        $this->listItem = $listItem;
    }


    /**
     * @Route("/{_locale<%app.supported_locales%>}/update-item", name="update-item")
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {

        if ($request->isMethod('POST')) {

            $id = $_POST['item'];
            return $this->redirectToRoute("update-item-id", ['id' => $id]);

        }

        $user = $this->getUser();
        $items = $this->listItem->listItemByUser($user);

        return $this->render('item/choose-item.html.twig', [
            'items' => $items,
            'action' => 'update'
        ]);
    }


    /**
     * @Route("/{_locale<%app.supported_locales%>}/update-item/{id}", name="update-item-id")
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function updateItem(string $id, Request $request)
    {
        $user = $this->getUser();
        $item = $this->listItem->listItemByUserAndId($user, $id);

        if ($item == null) {
            $this->denyAccessUnlessGranted('ROLE_ADMIN');
        }

        $formUpdate = $this->createForm(ItemType::class, $item);
        $formUpdate->handleRequest($request);

        if ($formUpdate->isSubmitted() && $formUpdate->isValid()) {

            $result = $this->updateItem->updateItem($item);

            if ($result) {
                $this->addFlash('success', Item::ITEM_UPDATED);
                return $this->redirect("/update-item/" . $id);
            } else {
                $this->addFlash('fail', Item::ITEM_FAIL);
                return $this->redirect("/update-item/" . $id);
            }
        }

        return $this->render('item/update-item.html.twig', [
            'item' => $item,
            'formUpdate' => $formUpdate->createView()
        ]);
    }
}