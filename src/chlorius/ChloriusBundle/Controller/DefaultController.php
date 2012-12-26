<?php

namespace chlorius\ChloriusBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Finder\Finder;

/**
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("", name="homepage")
     */
    public function indexAction($path = "")
    {
        return $this->redirect($this->generateUrl('chlorius_gallery_index'));
    }
}
