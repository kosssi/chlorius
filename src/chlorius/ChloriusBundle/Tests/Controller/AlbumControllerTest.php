<?php

namespace chlorius\ChloriusBundle\Tests\Controller;

use chlorius\ChloriusBundle\Tests\WebTestCase;

/**
 * Tests for the album controller
 * @author Simon Constans <kosssi@gmail.com>
 *
 * @group functional
 */
class AlbumControllerTest extends WebTestCase
{
    public function testIndexAlbums()
    {
        $client = $this->loginRoleUser();
        $crawler = $client->request('GET', '/a/demo');

        $this->assertUrl('/a/demo', $client);
        $this->assertCount(1, $crawler->filter('h1:contains("Albums")'));
    }

    public function testAddAlbum()
    {

    }
}
