<?php

namespace App\Model;

use App\Entity\User;
use App\Message\RoutingKey;
use App\Message\UserRegisterMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Register
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $bus;
    private WorkflowInterface $workflow;
    private TranslatorInterface $translator;
    private SpamChecker $spamChecker;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus,
                                WorkflowInterface $userStateMachine, TranslatorInterface $translator, SpamChecker $spamChecker)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
        $this->workflow = $userStateMachine;
        $this->translator = $translator;
        $this->spamChecker = $spamChecker;
    }

    public function register(User $user, array $context)
    {
        try {
            $this->entityManager->persist($user);

            $score = $this->spamChecker->getSpamScore($user, $context);
            $transition = 'accept';
            if (2 === $score) {
                $transition = 'reject';
            }

            $this->workflow->apply($user, $transition);
            $this->entityManager->flush();

            $this->bus->dispatch(
                new UserRegisterMessage($user->getId(), $user->getName(), $user->getState()),
                [new AmqpStamp(RoutingKey::USER_QUEUE)]
            );
            return true;

        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param string $transition
     * @param User $user
     * @return string[]
     */
    public function activate(string $transition, User $user)
    {

        $state = $user->getState();

        if ($transition == 'active' && $state != 'spam') {

            if ($this->workflow->can($user, 'deactivate')) {

                $transition = 'deactivate';
                $this->workflow->apply($user, 'deactivate');
                $this->entityManager->flush();

            } else if ($this->workflow->can($user, 'activate')) {

                $this->workflow->apply($user, 'activate');
                $this->entityManager->flush();
            }

            $statePost = $user->getState();
            return [
                'info' => $this->translator->trans('Applied workflow'),
                'transition' => $transition,
                'state' => $statePost
            ];

        } else if ($this->workflow->can($user, 'reject_inactive') && $transition == 'reject_inactive') {

            $this->workflow->apply($user, 'reject_inactive');
            $this->entityManager->flush();

            $statePost = $user->getState();
            return [
                'info' => $this->translator->trans('User rejected'),
                'transition' => $transition,
                'state' => $statePost
            ];

        } else if (($state == 'spam') && $transition == 'reject_inactive') {

            return [
                'info' => $this->translator->trans('Already in this workflow'),
                'transition' => $transition,
                'state' => $state
            ];

        } else {

            $statePost = $user->getState();
            return [
                'info' => $this->translator->trans('Error, cant apply this workflow'),
                'transition' => $transition,
                'state' => $statePost
            ];
        }
    }
}