<?php

namespace Mailer\MessageHandler;

use Exception;
use Mailer\Message\UserRegisterMessage;
use Mailer\Service\Mailer\ClientRoute;
use Mailer\Service\Mailer\MailerService;
use Mailer\Templating\TwigTemplate;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use function sprintf;

class UserRegisterMessageHandler implements MessageHandlerInterface
{
    private MailerService $mailerService;
    private string $host;
    private string $mailerDefaultSender;

    public function __construct(MailerService $mailerService, string $host, string $mailerDefaultSender)
    {
        $this->mailerService = $mailerService;
        $this->host = $host;
        $this->mailerDefaultSender = $mailerDefaultSender;
    }

    /**
     * @param UserRegisterMessage $message
     * @throws Exception
     */
    public function __invoke(UserRegisterMessage $message): void
    {

        $payload = [
            'name' => $message->getName(),
            'id' => $message->getId(),
            'state' => $message->getState(),
            'info' => $message->getInfo(),

            'url' => sprintf(
                '%s%s/%s',
                $this->host,
                ClientRoute::ACTIVATE_ACCOUNT,
                $message->getId()
            )
        ];

        $subject = 'User: ' . $payload['id'];

        if ($message->getInfo()) {
            $subject .= " SpamChecker Error";
            $this->mailerService->send($subject, $this->mailerDefaultSender, $payload, NotificationEmail::IMPORTANCE_MEDIUM);
        } else {
            $this->mailerService->send($subject, $this->mailerDefaultSender, $payload, NotificationEmail::IMPORTANCE_LOW);
        }
    }
}