<?php

namespace chlorius\ChloriusBundle\Tests\FOSUserBundle;

use chlorius\ChloriusBundle\Tests\WebTestCase;

/**
 * Tests for the FOSUser controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class ProfileShowPageTest extends WebTestCase
{
    public function testProfileShowPage()
    {
        /** @var $client \Symfony\Bundle\FrameworkBundle\Client **/
        $client = $this->loginRoleUser();
        $crawler = $client->request('GET', '/profile/');

        $this->assertUrl('/profile/', $client);

        $this->assertCount(1, $crawler->filter('input#username[value="demo"]'));
        $this->assertCount(1, $crawler->filter('input#email[value="demo@example.org"]'));

        return $client;
    }

    /**
     * @param $client \Symfony\Bundle\FrameworkBundle\Client
     *
     * @depends testProfileShowPage
     */
    public function testProfileShowLink($client)
    {
        $crawler = $client->getCrawler();

        $changePassword = $crawler->filter('li.button a:contains("Change password ?")');
        $editProfile = $crawler->filter('li.button a:contains("Edit")');

        $this->assertLink($client, $changePassword, 200, '/profile/change-password');
        $this->assertLink($client, $editProfile, 200, '/profile/edit');
    }
}
