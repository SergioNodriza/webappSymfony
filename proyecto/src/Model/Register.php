<?php

namespace App\Model;

use App\Entity\User;
use App\Message\RoutingKey;
use App\Message\UserRegisterMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class Register
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    public function register(User $user)
    {
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->bus->dispatch(
                new UserRegisterMessage($user->getId(), $user->getName()),
                [new AmqpStamp(RoutingKey::USER_QUEUE)]
            );
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param bool $activate
     * @param User $user
     * @return string
     */
    public function activate(bool $activate, User $user)
    {
        $activated = $user->getActive();
        if ($activated == true && $activate == true) {
            return "User already activated";
        }

        if ($activate == true){

            $user->setActive($activate);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return "User activated";

        } else {

            $this->entityManager->remove($user);
            $this->entityManager->flush();
            return "User denied";
        }
    }
}