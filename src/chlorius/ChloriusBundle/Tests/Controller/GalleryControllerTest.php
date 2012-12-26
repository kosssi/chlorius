<?php

namespace chlorius\ChloriusBundle\Tests\Controller;

use chlorius\ChloriusBundle\Tests\WebTestCase;

/**
 * Tests for the gallery controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class GalleryControllerTest extends WebTestCase
{
    public function testRedirectionLogin()
    {
        $client = static::createClient();
        $client->request('GET', '/gallery');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $client->followRedirect();
        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        return $client;
    }

    /**
     * @depends testRedirectionLogin
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     */
    public function testIndex($client)
    {
        $client = $this->loginRoleUser();
        $crawler = $client->request('GET', '/gallery');

        $this->assertCount(1, $crawler->filter('html:contains("Gamma Gallery")'));

        return $client;
    }

    /**
     * @depends testIndex
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     */
    function testMenuAccountLink($client)
    {
        /** @var $crawler \Symfony\Component\DomCrawler\Crawler */
        $crawler = $client->getCrawler();
        $this->assertCount(1, $crawler->filter('span a.account:contains("demo")'));

        $accountLink = $crawler->filter('a.account:contains("demo")')->link();
        $client->click($accountLink);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('/account', $client->getRequest()->getRequestUri());
    }

}
