<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Model\FlashMessage;
use App\Model\NewItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewItemController extends AbstractController
{
    private NewItem $newItem;

    public function __construct(NewItem $newItem)
    {
        $this->newItem = $newItem;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/new-item", name="new-item")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $result = $this->newItem->newItem($user, $item);

            if ($result) {
                $this->addFlash('success', FlashMessage::ITEM_OK);
                return $this->redirectToRoute("new-item");
            } else {
                $this->addFlash('fail', FlashMessage::ITEM_FAIL);
                return $this->redirectToRoute("new-item");
            }
        }

        return $this->render('item/new-item.html.twig', [
            'formNewItem' => $form->createView(),
        ]);
    }
}