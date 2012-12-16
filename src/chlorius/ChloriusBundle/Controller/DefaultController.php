<?php

namespace chlorius\ChloriusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;

/**
 * @Route("/gallery")
 */
class DefaultController extends Controller
{
    /**
     * @Route("{path}", requirements={"path" = ".*"}, defaults={"path" = ""})
     * @Template()
     */
    public function indexAction($path = "")
    {
        /** @var $chloriusHelper \kosssi\GammaGalleryBundle\Helper\ChloriusHelper */
        $chloriusHelper = $this->get('chlorius.helper');
        $dir = $chloriusHelper->photoAlbumsDir . $path;
        //$chloriusHelper->fixOrientationAlbum($dir);
        $photos = $chloriusHelper->getPhotos($dir);

        $title = 'Gamma Gallery';
        $description = 'A responsive image gallery experiment';

        return array(
            'albumsDir' => 'albums/',
            'photos' => $photos,
            'title' => $title,
            'description' => $description
        );
    }

    public function uploadAction()
    {

    }

    /**
     * @Route("/fix-orientation")
     * @Template()
     */
    public function fixOrientationAction()
    {
        /** @var $chloriusHelper \kosssi\GammaGalleryBundle\Helper\ChloriusHelper */
        $chloriusHelper = $this->get('chlorius.helper');

        $countPhotosModified = $chloriusHelper->fixOrientationAlbum($chloriusHelper->photoAlbumsDir);

        return array('countPhotosModified' => $countPhotosModified);
    }
}
