<?php

namespace App\Model;

use App\Entity\User;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{
    const SPAM_CHECKER_EXCEPTION = 3;

    private HttpClientInterface $client;
    private string $endpoint;

    public function __construct(HttpClientInterface $client, string $akismetKey)
    {

        $this->client = $client;
        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
    }

    /**
     * @param User $user
     * @param array $context
     * @return int
     */
    public function getSpamScore(User $user, array $context): int
    {
        try {
            $response = $this->client->request('POST', $this->endpoint, [
                'body' => array_merge($context, [
                    'blog' => 'https://webapp.example.com',
                    'user_type' => 'user',
                    'user_name' => $user->getName(),
                    'app_lang' => 'en',
                    'blog_charset' => 'UTF-8',
                    'is_test' => true,
                ]),
            ]);

            $headers = $response->getHeaders();
            if ('discard' === ($headers['x-akismet-pro-tip'][0] ?? '')) {
                return 2;
            }

            $content = $response->getContent();
            if (isset($headers['x-akismet-debug-help'][0])) {
                throw new RuntimeException(sprintf('Unable to check for spam: %s (%s).', $content, $headers['x-akismet-debug-help'][0]));
            }

            return 'true' === $content ? 1 : 0;

        } catch (TransportExceptionInterface $e) {
            return self::SPAM_CHECKER_EXCEPTION;
        } catch (ClientExceptionInterface $e) {
            return self::SPAM_CHECKER_EXCEPTION;
        } catch (RedirectionExceptionInterface $e) {
            return self::SPAM_CHECKER_EXCEPTION;
        } catch (ServerExceptionInterface $e) {
            return self::SPAM_CHECKER_EXCEPTION;
        }
    }
}