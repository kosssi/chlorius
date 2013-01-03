<?php

namespace chlorius\ChloriusBundle\Tests\FOSUserBundle;

use chlorius\ChloriusBundle\Tests\WebTestCase;

/**
 * Tests for the FOSUser controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class RegisterPageTest extends WebTestCase
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::loadUser();
    }

    public function testRegisterPage()
    {
        $client = static::createClient();
        $client->request('GET', '/register/');

        $this->assertUrl('/register/', $client);

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testRegisterPage
     */
    public function testRegisterLink($client)
    {
        $this->assertLink(
            $client,
            $client->getCrawler()->filter('li.button a:contains("Already sign up ?")'),
            200,
            '/login'
        );

        $client->request('GET', '/register/');

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testRegisterLink
     */
    public function testRegisterFormError($client)
    {
        $form = $client->getCrawler()->selectButton('Sign up')->form();
        $crawler = $client->submit(
            $form,
            array(
                'fos_user_registration_form[username]' => 'demo',
                'fos_user_registration_form[email]' => self::USER_EMAIL,
                'fos_user_registration_form[plainPassword][first]' => 'test',
                'fos_user_registration_form[plainPassword][second]' => ''
            )
        );
        $this->assertUrl('/register/', $client);

        $this->assertCount(3, $crawler->filter('input.error'));
        $this->assertCount(1, $crawler->filter('input[title="fos_user.username.already_used"]'));
        $this->assertCount(1, $crawler->filter('input[title="fos_user.email.already_used"]'));
        $this->assertCount(1, $crawler->filter('input[title="fos_user.password.mismatch"]'));

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testRegisterFormError
     */
    public function testRegisterFormValid($client)
    {
        $form = $client->getCrawler()->selectButton('Sign up')->form();
        $client->submit(
            $form,
            array(
                'fos_user_registration_form[username]' => 'test',
                'fos_user_registration_form[email]' => 'test@example.org',
                'fos_user_registration_form[plainPassword][first]' => 'testtest',
                'fos_user_registration_form[plainPassword][second]' => 'testtest'
            )
        );
        $this->assertUrl('/register/', $client, 302);
        $client->followRedirect();

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testRegisterFormValid
     */
    public function testRegistrerConfirmed($client)
    {
        $this->assertUrl('/register/confirmed', $client);
        $this->assertLink(
            $client,
            $client->getCrawler()->filter('li.button a:contains("Home")'),
            200,
            '/gallery'
        );
    }
}
