<?php

namespace Mailer\Service\Mailer;

use Mailer\Templating\TwigTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use function sprintf;

class MailerService
{

    private MailerInterface $mailer;
    private Environment $twig;
    private LoggerInterface $logger;
    private string $mailerDefaultSender;

    public function __construct(MailerInterface $mailer, Environment $twig, LoggerInterface $logger, string $mailerDefaultSender)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->logger = $logger;
        $this->mailerDefaultSender = $mailerDefaultSender;
    }

    /**
     * @param string $subject
     * @param string $receiver
     * @param array $payload
     * @param string $importance
     */
    public function send(string $subject, string $receiver, array $payload, string $importance): void
    {
        $email = (new NotificationEmail())
            ->subject($subject)
            ->htmlTemplate(TwigTemplate::USER_REGISTER_EMAIL)
            ->from($this->mailerDefaultSender)
            ->to($receiver)
            ->context([
                'id' => $payload['id'],
                'name' => $payload['name'],
                'state' => $payload['state'],
                'info' => $payload['info'],
                'url' => $payload['url']
            ])
            ->importance($importance);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(sprintf('Error sending email: %s', $e->getMessage()));
        }
    }
}