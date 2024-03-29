<?php
/*
  ÁTICA - Aplicación web para la gestión documental de centros educativos

  Copyright (C) 2015-2016: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Tests\Controller;

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
                '_username' => 'admin',
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
                '_username' => 'admin',
                '_password' => 'admin'
            ]
        );
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect($frontpageUrl));
    }
}
