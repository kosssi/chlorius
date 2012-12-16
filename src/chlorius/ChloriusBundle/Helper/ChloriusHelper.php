<?php

namespace chlorius\ChloriusBundle\Helper;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class ChloriusHelper
{
    public $photoAlbumsDir;
    /** @var \Imagine\Gd\Imagine */
    private $imagine;

    function __construct($photoAlbumsDir, $imagine)
    {
        $this->photoAlbumsDir = $photoAlbumsDir;
        $this->imagine = $imagine;
    }

    public function getAlbums($dir)
    {
        $finder = new Finder();

        return $finder->directories()->in($dir)->sortByName();
    }

    public function getPhotos($dir)
    {
        if ($this->directoryExist($dir)) {
            $finder = new Finder();

            return $finder->files()->in($dir)->sortByName();
        }

        return false;
    }

    public function fixOrientationAlbum($dir)
    {
        $countPhotosModified = 0;

        if ($this->directoryExist($dir)) {
            $photos = $this->getPhotos($dir);
            foreach ($photos as $photo) {
                if ($this->fixOrientationPhoto($photo)) {
                    $countPhotosModified++;
                }
            }
        }

        return $countPhotosModified;
    }

    public function fixOrientationPhoto($photoPath)
    {
        $type = exif_imagetype($photoPath);

        if ($type) {
            $degrees = $this->rotate($photoPath);

            if ($degrees != 0) {
                $photo = $this->imagine->open($photoPath);
                $photo->rotate($degrees);
                $photo->save($photoPath);

                return true;
            }
        }

        return false;
    }

    public function countRotate($photos)
    {
        $countPhotosModified = 0;

        foreach ($photos as $photo) {
            if ($this->rotate($photo)) {
                $countPhotosModified++;
            }
        }

        return $countPhotosModified;
    }

    private function rotate($photoPath)
    {
        $degrees = 0;
        $exif = exif_read_data($photoPath);

        if (isset($exif['Orientation']) && $exif['Orientation'] != 1) {

            switch ($exif['Orientation']) {
                case 3:
                    $degrees = 180;
                    break;
                case 6:
                    $degrees = 90;
                    break;
                case 8:
                    $degrees = 270;
                    break;
            }
        }

        return $degrees;
    }

    private function directoryExist($directory)
    {
        $fsm = new Filesystem();
        if ($fsm->exists($directory)) {

            return true;
        }

        return false;
    }
}
