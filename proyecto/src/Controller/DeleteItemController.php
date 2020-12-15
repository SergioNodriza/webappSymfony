<?php

namespace App\Controller;

use App\Model\DeleteItem;
use App\Model\FlashMessage;
use App\Model\ListItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeleteItemController extends AbstractController
{
    private ListItem $listItem;
    private DeleteItem $deleteItem;
    private TranslatorInterface $translator;

    public function __construct(ListItem $listItem, DeleteItem $deleteItem, TranslatorInterface $translator)
    {
        $this->listItem = $listItem;
        $this->deleteItem = $deleteItem;
        $this->translator = $translator;
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/delete", name="delete-item")
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {

        if ($request->isMethod('POST')) {

            $id = $_POST['item'];
            return $this->redirectToRoute("delete-item-id", ['id' => $id]);
        }

        $user = $this->getUser();
        $items = $this->listItem->listItemByUser($user);

        return $this->render('item/choose-item.html.twig', [
            'items' => $items,
            'action' => 'delete'
        ]);
    }

    /**
     * @Route("/{_locale<%app.supported_locales%>}/delete-item/{id}", name="delete-item-id")
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
                $message = $this->translator->trans(FlashMessage::ITEM_DELETED);
                $this->addFlash('success', $message);
                return $this->redirectToRoute("delete-item");
            } else {
                $message = $this->translator->trans(FlashMessage::ITEM_FAIL);
                $this->addFlash('success', $message);
                return $this->redirectToRoute("delete-item-id", ['id' => $id]);
            }
        }

        return $this->render('item/delete-item.html.twig', [
            'item' => $item
        ]);
    }
}
