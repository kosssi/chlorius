<?php

namespace chlorius\ChloriusBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GalleryControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/gallery');

        $this->assertTrue($crawler->filter('html:contains("Gamma Gallery")')->count() > 0);
    }
}
