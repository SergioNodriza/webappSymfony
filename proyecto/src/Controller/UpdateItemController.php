<?php

namespace App\Controller;

use App\Entity\Item;
use App\Form\ItemType;
use App\Model\FlashMessage;
use App\Model\ListItem;
use App\Model\UpdateItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UpdateItemController extends AbstractController
{
    private UpdateItem $updateItem;
    private ListItem $listItem;
    private TranslatorInterface $translator;

    public function __construct(ListItem $listItem, UpdateItem $updateItem, TranslatorInterface $translator)
    {
        $this->updateItem = $updateItem;
        $this->listItem = $listItem;
        $this->translator = $translator;
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
                $message = $this->translator->trans(FlashMessage::ITEM_UPDATED);
                $this->addFlash('success', $message);
                return $this->redirectToRoute("update-item-id", ['id' => $id]);
            } else {
                $message = $this->translator->trans(FlashMessage::ITEM_FAIL);
                $this->addFlash('success', $message);
                return $this->redirectToRoute("update-item-id", ['id' => $id]);
            }
        }

        return $this->render('item/update-item.html.twig', [
            'item' => $item,
            'formUpdate' => $formUpdate->createView()
        ]);
    }
}