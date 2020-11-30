<?php

namespace App\Model;

use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class NewItem
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {

        $this->entityManager = $entityManager;
    }

    /**
     * @param UserInterface $user
     * @param Item $item
     * @return bool
     */
    public function newItem(UserInterface $user, Item $item)
    {
        try {
            $item->setUser($user);
            $this->entityManager->persist($item);
            $this->entityManager->flush();
            return true;

        } catch (Exception $e) {

            return false;
        }
    }
}