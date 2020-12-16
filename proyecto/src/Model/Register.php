<?php

namespace App\Model;

use App\Entity\User;
use App\Message\RoutingKey;
use App\Message\UserRegisterMessage;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Register
{
    const OK = 1;
    const SPAM = 2;
    const SPAM_CHECKER_EXCEPTION = 3;
    const GENERAL_EXCEPTION = 4;

    private EntityManagerInterface $entityManager;
    private MessageBusInterface $bus;
    private WorkflowInterface $workflow;
    private TranslatorInterface $translator;
    private SpamChecker $spamChecker;
    private int $result;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus,
                                WorkflowInterface $userStateMachine, TranslatorInterface $translator, SpamChecker $spamChecker)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
        $this->workflow = $userStateMachine;
        $this->translator = $translator;
        $this->spamChecker = $spamChecker;
        $this->result = self::OK;
    }

    /**
     * @param User $user
     * @param array $context
     * @return int
     */
    public function register(User $user, array $context)
    {
        try {
            $this->entityManager->persist($user);

            $score = $this->spamChecker->getSpamScore($user, $context);

            switch ($score) {
                case Register::SPAM:
                    $transition = 'reject';
                    $this->result = self::SPAM;
                    break;
                case Register::SPAM_CHECKER_EXCEPTION:
                    $transition = 'accept';
                    $this->result = self::SPAM_CHECKER_EXCEPTION;
                    break;
                default:
                    $transition = 'accept';
                    break;
            }

            $this->workflow->apply($user, $transition);
            $this->entityManager->flush();

            $this->bus->dispatch(
                new UserRegisterMessage($user->getId(), $user->getName(), $user->getState()),
                [new AmqpStamp(RoutingKey::USER_QUEUE)]
            );

        } catch (Exception $exception) {
            $this->result = self::GENERAL_EXCEPTION;
        }

        return $this->result;
    }

    /**
     * @param string $transition
     * @param User $user
     * @return string[]
     */
    public function activate(string $transition, User $user)
    {
        $state = $user->getState();

        if ($this->workflow->can($user, 'deactivate') && $transition == 'deactivate') {

            $this->workflow->apply($user, 'deactivate');
            $this->entityManager->flush();

            $statePost = $user->getState();
            return [
                'info' => $this->translator->trans('Applied workflow'),
                'transition' => $transition,
                'state' => $statePost,
                'id' => $user->getId()
            ];

        } else if ($this->workflow->can($user, 'activate') && $transition == 'activate') {

            $this->workflow->apply($user, 'activate');
            $this->entityManager->flush();

            $statePost = $user->getState();
            return [
                'info' => $this->translator->trans('Applied workflow'),
                'transition' => $transition,
                'state' => $statePost,
                'id' => $user->getId()
            ];
        }

        if ($this->workflow->can($user, 'reject_inactive') && $transition == 'reject') {

            $this->workflow->apply($user, 'reject_inactive');
            $this->entityManager->flush();

            $statePost = $user->getState();
            return [
                'info' => $this->translator->trans('User rejected'),
                'transition' => $transition,
                'state' => $statePost,
                'id' => $user->getId()
            ];

        } else if ($state == 'spam' && $transition == 'reject') {

            return [
                'info' => $this->translator->trans('Already in this workflow'),
                'transition' => $transition,
                'state' => $state,
                'id' => $user->getId()
            ];

        } else {

            $statePost = $user->getState();
            return [
                'info' => $this->translator->trans('Error, cant apply this workflow'),
                'transition' => $transition,
                'state' => $statePost,
                'id' => $user->getId()
            ];
        }
    }
}