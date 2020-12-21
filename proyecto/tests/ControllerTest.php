<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();

        $client->request('GET', '/en/login');
        $this->assertResponseIsSuccessful();
    }
}