<?php

namespace AppBundle\Tests\Controller;

use Monolog\ErrorHandler;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $loginUrl = $client->getContainer()->get('router')->generate('login');
        $client->request('GET', $loginUrl);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testFailedLogin()
    {
        $client = static::createClient();

        $loginUrl = $client->getContainer()->get('router')->generate('login', [], true);
        $client->request('GET', $loginUrl);
        $form = $client->getCrawler()->selectButton('login')->form();
        $form->setValues([
                '_username' => 'admin@example.com',
                '_password' => 'wrong'
            ]
        );
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode($loginUrl));
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testSuccessLogin()
    {
        $client = static::createClient();

        $loginUrl = $client->getContainer()->get('router')->generate('login', [], true);
        $frontpageUrl = $client->getContainer()->get('router')->generate('frontpage', [], true);

        $client->request('GET', $loginUrl);
        $form = $client->getCrawler()->selectButton('login')->form();
        $form->setValues([
                '_username' => 'admin@example.com',
                '_password' => 'admin'
            ]
        );
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect($frontpageUrl));
    }
}
