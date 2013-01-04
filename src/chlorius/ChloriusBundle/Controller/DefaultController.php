<?php

namespace chlorius\ChloriusBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
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
     * @Secure(roles="ROLE_USER")
     */
    public function indexAction()
    {

        return $this->redirect($this->generateUrl('chlorius_gallery_index'));
    }
}
