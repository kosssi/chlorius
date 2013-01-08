<?php

namespace chlorius\ChloriusBundle\Helper;

use chlorius\ChloriusBundle\Entity\User;
use chlorius\ChloriusBundle\Entity\Album;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class ChloriusHelper
{
    public $photoAlbumsDir;
    /** @var \Imagine\Gd\Imagine */
    private $imagine;

    /**
     * @param $photoAlbumsDir
     * @param $imagine
     */
    function __construct($photoAlbumsDir, $imagine)
    {
        $this->photoAlbumsDir = $photoAlbumsDir;
        $this->imagine = $imagine;
    }

    /**
     * @param $dir
     * @return null|\Symfony\Component\Finder\Finder
     */
    public function getAlbums($dir)
    {
        if ($this->directoryExist($dir)) {
            $finder = new Finder();

            return $finder->directories()->in($dir)->depth(0)->sortByName();
        }

        return null;
    }

    /**
     * @param $dir
     * @return null|\Symfony\Component\Finder\Finder
     */
    public function getPhotos($dir)
    {
        if ($this->directoryExist($dir)) {
            $finder = new Finder();

            return $finder->files('*.(jpeg|jpg|png)')->in($dir)->depth(0)->sortByName();
        }

        return null;
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

    public function createAlbumFolder(User $user, Album $album)
    {
        $fsm = new Filesystem();

        $fsm->mkdir($this->photoAlbumsDir . '/' . $user->getUsername() . '/' . $album->getId());
    }

    public function getAlbumDirectory(User $user, Album $album)
    {

        return $this->photoAlbumsDir . '/' . $user->getUsername() . '/' . $album->getId();
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
