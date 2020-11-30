<?php

namespace App\Model;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Symfony\Component\Security\Core\User\UserInterface;

class ListItem
{
    private ItemRepository $itemRepository;

    public function __construct(ItemRepository $itemRepository) {
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param UserInterface $user
     * @return Item[]
     */
    public function listItemByUser(UserInterface $user) {
        return $this->itemRepository->findBy(['user' => $user]);
    }

    /**
     * @param UserInterface $user
     * @param string $id
     * @return Item|null
     */
    public function listItemByUserAndId(UserInterface $user, string $id): ?Item
    {
        return $this->itemRepository->findOneBy(["id" => $id, "user" => $user]);
    }
}