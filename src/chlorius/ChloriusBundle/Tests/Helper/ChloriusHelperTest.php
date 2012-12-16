<?php

namespace chlorius\ChloriusBundle\Tests\Helper;

use Imagine\Gd\Imagine;
use chlorius\ChloriusBundle\Helper\ChloriusHelper;

/**
 * @package ChloriusBundle
 * @author Simon Constans <kosssi@gmail.com>
 */
class ChloriusHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var \chlorius\ChloriusBundle\Helper\ChloriusHelper */
    private static $chloriusHelper;
    private static $albumsDir;
    private static $photoPath;

    public static function setUpBeforeClass()
    {
        self::$albumsDir = __DIR__ . '/../data';
        self::$photoPath = self::$albumsDir . '/albums/DSC06304.JPG';

        $imagine = new Imagine();
        self::$chloriusHelper = new ChloriusHelper(self::$albumsDir, $imagine);
    }

    public function testGetAlbums()
    {
        $albums = self::$chloriusHelper->getAlbums(self::$chloriusHelper->photoAlbumsDir);
        $this->assertCount(1, $albums);

        $albums = self::$chloriusHelper->getAlbums(self::$chloriusHelper->photoAlbumsDir . '/albums');
        $this->assertCount(0, $albums);

        $albums = self::$chloriusHelper->getAlbums(self::$chloriusHelper->photoAlbumsDir . '/null');
        $this->assertNull($albums);
    }

    public function testGetPhotos()
    {
        $photos = self::$chloriusHelper->getPhotos(self::$chloriusHelper->photoAlbumsDir);
        $this->assertCount(0, $photos);

        $photos = self::$chloriusHelper->getPhotos(self::$chloriusHelper->photoAlbumsDir . '/null');
        $this->assertNull($photos);

        $photos = self::$chloriusHelper->getPhotos(self::$chloriusHelper->photoAlbumsDir . '/albums');
        $this->assertCount(1, $photos);

        return $photos;
    }

    /**
     * @depends testGetPhotos
     */
    public function testCountRotate($photos)
    {
        $countRotate = self::$chloriusHelper->countRotate($photos);
        $this->assertEquals(1, $countRotate);
    }
}
