<?php

namespace App\Model;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class UpdateItem
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
    public function updateItem(Item $item) {

        try {
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}