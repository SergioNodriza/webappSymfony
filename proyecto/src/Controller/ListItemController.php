<?php


namespace App\Controller;

use App\Model\ListItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListItemController extends AbstractController
{
    private ListItem $listItem;

    public function __construct(ListItem $listItem)
    {
        $this->listItem = $listItem;
    }

    /**
     * @Route("/", name="inicio")
     * @Route("/{_locale<%app.supported_locales%>}", name="inicioLocale")
     * @return Response
     */
    public function list(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($_SERVER['REQUEST_URI'] == "/") return $this->redirectToRoute("inicioLocale");

        $user = $this->getUser();
        $items = $this->listItem->listItemByUser($user);

        return $this->render('item/list-item.html.twig', [
            'items' => $items,
        ]);
    }
}