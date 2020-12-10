<?php

namespace Mailer\Service\Mailer;

use Exception;
use Mailer\Templating\TwigTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

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
     * @param string $receiver
     * @param string $template
     * @param array $payload
     * @throws Exception
     */
    public function send(string $receiver, string $template, array $payload): void
    {
        $email = (new NotificationEmail())
            ->subject('User: ' . $payload['id'])
            ->htmlTemplate($template)
            ->from($this->mailerDefaultSender)
            ->to($receiver)
            ->context([
                'id' => $payload['id'],
                'name' => $payload['name'],
                'state' => $payload['state'],
                'url' => $payload['url']
            ])
            ->importance(NotificationEmail::IMPORTANCE_LOW);
        ;

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(\sprintf('Error sending email: %s', $e->getMessage()));
        }
    }
}