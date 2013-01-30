<?php

namespace chlorius\ChloriusBundle\Controller;

use chlorius\ChloriusBundle\Entity\Album;
use chlorius\ChloriusBundle\Entity\User;
use chlorius\ChloriusBundle\Form\Type\AlbumType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/a/{username}")
 * @ParamConverter("user", class="ChloriusBundle:User", options={"username" = "username"})
 */
class AlbumController extends Controller
{
    /**
     * @Secure(roles="ROLE_USER")
     * @Route("", name="chlorius_album_index")
     * @Template()
     */
    public function indexAction(User $user)
    {

        return array('username' => $user->getUsername(), 'albums' => $user->getAlbums());
    }

    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/new", name="chlorius_album_new")
     * @Template()
     */
    public function newAction(User $user)
    {
        $form = $this->get('form.factory')->create(new AlbumType());

        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                /** @var $album Album */
                $album = $form->getData();
                $album->setDate(new \DateTime());
                $album->setUser($user);

                $em = $this->getDoctrine()->getManager();
                $em->persist($album);
                $em->flush();

                /** @var $chloriusHelper \chlorius\ChloriusBundle\Helper\ChloriusHelper */
                $chloriusHelper = $this->get('chlorius.helper');
                $chloriusHelper->createAlbumFolder($user, $album);

                $this->get('session')->setFlash('success', 'L\'album a été correctement crée.');

                return $this->redirect(
                    $this->generateUrl(
                        'chlorius_album_index',
                        array(
                            'username' => $user->getUsername()
                        )
                    )
                );
            }
        }

        return array('username' => $user->getUsername(), 'form' => $form->createView());
    }

    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/{slug}/remove", name="chlorius_album_remove")
     * @ParamConverter("album", class="ChloriusBundle:Album", options={"slug" = "slug"})
     * @Template()
     */
    public function removeAction(User $user, Album $album)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($album);
        $em->flush();

        $this->get('session')->setFlash('success', 'L\'album a été correctement supprimé.');

        return $this->redirect($this->generateUrl('chlorius_album_index', array('username' => $user->getUsername())));
    }

    /**
     * @Secure(roles="ROLE_USER")
     * @Route("/{slug}", name="chlorius_album_show")
     * @ParamConverter("album", class="ChloriusBundle:Album", options={"slug" = "slug"})
     * @Template()
     */
    public function showAction(User $user, Album $album)
    {
        /** @var $chloriusHelper \chlorius\ChloriusBundle\Helper\ChloriusHelper */
        $chloriusHelper = $this->get('chlorius.helper');
        $albumDir = $chloriusHelper->getAlbumDirectory($user, $album);
        $photos = $chloriusHelper->getPhotos($albumDir);
        $albumDir = strstr($albumDir, 'albums/');

        return array(
            'username' => $user->getUsername(),
            'album' => $album,
            'photos' => $photos,
            'albumDir' => $albumDir
        );
    }
}
