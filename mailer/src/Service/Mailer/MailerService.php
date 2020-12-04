<?php

namespace Mailer\Service\Mailer;

use Exception;
use Mailer\Templating\TwigTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{

    private MailerInterface $mailer;
    private Environment $engine;
    private LoggerInterface $logger;
    private string $mailerDefaultSender;

    public function __construct(MailerInterface $mailer, Environment $engine, LoggerInterface $logger, string $mailerDefaultSender)
    {
        $this->mailer = $mailer;
        $this->engine = $engine;
        $this->logger = $logger;
        $this->mailerDefaultSender = $mailerDefaultSender;
    }

    /**
     * @param string $receiver
     * @param string $id
     * @param string $template
     * @param array $payload
     * @throws Exception
     */
    public function send(string $receiver, string $id, string $template, array $payload): void
    {
        $email = (new Email())
            ->from($this->mailerDefaultSender)
            ->to($receiver)
            ->subject('User: ' . $id)
            ->html($this->engine->render($template, $payload));

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(\sprintf('Error sending email: %s', $e->getMessage()));
        }
    }
}