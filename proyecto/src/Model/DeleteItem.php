<?php

namespace App\Model;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;

class DeleteItem
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Item $item
     * @return bool
     */
    public function deleteItem(Item $item) {

        try {
            $this->entityManager->remove($item);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }
}