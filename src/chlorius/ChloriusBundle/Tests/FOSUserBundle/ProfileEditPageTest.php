<?php

namespace chlorius\ChloriusBundle\Tests\FOSUserBundle;

use chlorius\ChloriusBundle\Tests\WebTestCase;

/**
 * Tests for the FOSUser controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class ProfileEditPageTest extends WebTestCase
{
    public function testProfileEditPage()
    {
        /** @var $client \Symfony\Bundle\FrameworkBundle\Client **/
        $client = $this->loginRoleUser();
        $crawler = $client->request('GET', '/profile/edit');

        $this->assertUrl('/profile/edit', $client);

        $this->assertCount(1, $crawler->filter('input#username[value="demo"]'));
        $this->assertCount(1, $crawler->filter('input#email[value="demo@example.org"]'));
        $this->assertCount(1, $crawler->filter('input#password'));

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testProfileEditPage
     */
    public function testProfileEditLink($client)
    {
        $crawler = $client->getCrawler();

        $changePassword = $crawler->filter('li.button a:contains("Change password ?")');
        $back = $crawler->filter('li.button a:contains("Back")');

        $this->assertLink($client, $changePassword, 200, '/profile/change-password');
        $this->assertLink($client, $back, 200, '/profile/');

        $client->request('GET', '/profile/edit');

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testProfileEditLink
     */
    public function testProfileFormError($client)
    {
        $form = $client->getCrawler()->selectButton('>')->form();
        $client->submit($form, array(
                'fos_user_profile_form[username]' => self::USER_LOGIN,
                'fos_user_profile_form[email]' => self::USER_EMAIL,
                'fos_user_profile_form[current_password]' => self::USER_EMAIL
            )
        );
        $this->assertUrl('/profile/edit', $client, 200);
        $this->assertCount(1, $client->getCrawler()->filter('input.error'));

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testProfileFormError
     */
    public function testProfileFormValid($client)
    {
        $form = $client->getCrawler()->selectButton('>')->form();
        $client->submit($form, array(
                'fos_user_profile_form[username]' => self::USER_LOGIN,
                'fos_user_profile_form[email]' => self::USER_EMAIL,
                'fos_user_profile_form[current_password]' => self::USER_PASS
            )
        );
        $this->assertUrl('/profile/edit', $client, 302);
        $client->followRedirect();
        $this->assertUrl('/profile/', $client, 200);

        $this->assertCount(1, $client->getCrawler()->filter('p.valid'));
    }
}
