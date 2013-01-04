<?php

namespace chlorius\ChloriusBundle\Tests\FOSUserBundle;

use chlorius\ChloriusBundle\Tests\WebTestCase;

/**
 * Tests for the FOSUser controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class ChangePasswordPageTest extends WebTestCase
{
    public function testChangePasswordPage()
    {
        /** @var $client \Symfony\Bundle\FrameworkBundle\Client **/
        $client = $this->loginRoleUser();
        $crawler = $client->request('GET', '/profile/change-password');

        $this->assertUrl('/profile/change-password', $client);
        $this->assertCount(3, $crawler->filter('input[type="password"]'));

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testChangePasswordPage
     */
    public function testChangePasswordLink($client)
    {
        $crawler = $client->getCrawler();

        $back = $crawler->filter('li.button a:contains("Back")');

        $this->assertLink($client, $back, 200, '/profile/');

        $client->request('GET', '/profile/change-password');

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testChangePasswordLink
     */
    public function testChangePasswordFormError($client)
    {
        $form = $client->getCrawler()->selectButton('>')->form();
        $crawler = $client->submit($form, array(
                'fos_user_change_password_form[current_password]' => self::USER_PASS,
                'fos_user_change_password_form[new][first]' => self::USER_PASS,
                'fos_user_change_password_form[new][second]' => self::USER_EMAIL
            )
        );
        $this->assertUrl('/profile/change-password', $client, 200);
        $this->assertCount(1, $crawler->filter('input.error'));

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testChangePasswordFormError
     */
    public function testChangePasswordFormValid($client)
    {
        $form = $client->getCrawler()->selectButton('>')->form();
        $client->submit($form, array(
                'fos_user_change_password_form[current_password]' => self::USER_PASS,
                'fos_user_change_password_form[new][first]' => self::USER_PASS,
                'fos_user_change_password_form[new][second]' => self::USER_PASS
            )
        );
        $this->assertUrl('/profile/change-password', $client, 302);
        $client->followRedirect();
        $this->assertUrl('/profile/', $client, 200);

        $this->assertCount(1, $client->getCrawler()->filter('p.valid'));
    }
}
