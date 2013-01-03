<?php

namespace chlorius\ChloriusBundle\Tests\FOSUserBundle;

use chlorius\ChloriusBundle\Tests\WebTestCase;

/**
 * Tests for the FOSUser controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class LoginPageTest extends WebTestCase
{
    public function testLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertUrl('/login', $client);

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testLoginPage
     */
    public function testLoginLink($client)
    {
        $crawler = $client->getCrawler();

        $register = $crawler->filter('li.button a:contains("Join us ?")');
        $resetting = $crawler->filter('li.button a:contains("Lost password ?")');

        $this->assertLink($client, $register, 200, '/register/');
        $this->assertLink($client, $resetting, 200, '/resetting/request');

        $client->request('GET', '/login');

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testLoginLink
     */
    public function testLoginFormError($client)
    {
        $form = $client->getCrawler()->selectButton('>')->form();
        $client->submit($form, array('_username' => self::USER_EMAIL, '_password' => self::USER_EMAIL));
        $this->assertUrl('/verification', $client, 302);
        $crawler = $client->followRedirect();
        $this->assertUrl('/login', $client);
        $this->assertCount(3, $crawler->filter('input.error'));
        $this->assertCount(2, $crawler->filter('input[title="Bad credentials"]'));

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testLoginFormError
     */
    public function testLoginFormValid($client)
    {
        $form = $client->getCrawler()->selectButton('>')->form();
        $client->submit($form, array('_username' => self::USER_EMAIL, '_password' => self::USER_PASS));
        $this->assertUrl('/verification', $client, 302);
        $client->followRedirect();
        $this->assertUrl('/', $client, 302);
        $client->followRedirect();
        $this->assertUrl('/gallery', $client);
    }
}
