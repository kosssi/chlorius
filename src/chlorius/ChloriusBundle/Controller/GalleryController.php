<?php

namespace chlorius\ChloriusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;

/**
 * @Route("/gallery")
 */
class GalleryController extends Controller
{
    /**
     * @Route("/fix-orientation{path}", requirements={"path" = ".*"}, defaults={"path" = ""}, name="chlorius_fix_orientation")
     */
    public function fixOrientationAction($path = "")
    {
        /** @var $chloriusHelper \chlorius\ChloriusBundle\Helper\ChloriusHelper */
        $chloriusHelper = $this->get('chlorius.helper');
        $dir = $chloriusHelper->photoAlbumsDir . $path;
        $countPhotosModified = $chloriusHelper->fixOrientationAlbum($dir);

        return $this->redirect($this->generateUrl('chlorius_gallery'));
    }

    /**
     * @Route("{path}", requirements={"path" = ".*"}, defaults={"path" = ""}, name="chlorius_gallery")
     * @Template()
     */
    public function indexAction($path = "")
    {
        /** @var $chloriusHelper \chlorius\ChloriusBundle\Helper\ChloriusHelper */
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
}
