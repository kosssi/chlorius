<?php

namespace chlorius\ChloriusBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests for the default controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());
    }
}
