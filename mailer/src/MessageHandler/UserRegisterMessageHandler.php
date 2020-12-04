<?php

namespace Mailer\MessageHandler;

use Exception;
use Mailer\Message\UserRegisterMessage;
use Mailer\Service\Mailer\ClientRoute;
use Mailer\Service\Mailer\MailerService;
use Mailer\Templating\TwigTemplate;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

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

            'urlActivate' => \sprintf(
                '%s%s/%s?%s',
                $this->host,
                ClientRoute::ACTIVATE_ACCOUNT,
                $message->getId(),
                "state=active"
            ),
            'urlReject_inactive' => \sprintf(
                '%s%s/%s?%s',
                $this->host,
                ClientRoute::ACTIVATE_ACCOUNT,
                $message->getId(),
                "state=reject_inactive"
            )
        ];

        $this->mailerService->send($this->mailerDefaultSender, $message->getId(),TwigTemplate::USER_REGISTER, $payload);
    }
}