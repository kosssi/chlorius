<?php

namespace chlorius\ChloriusBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Album", mappedBy="user")
     */
    private $albums;

    public function __construct()
    {
        parent::__construct();

        $this->albums = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add albums
     *
     * @param \chlorius\ChloriusBundle\Entity\Album $albums
     * @return User
     */
    public function addAlbum(\chlorius\ChloriusBundle\Entity\Album $albums)
    {
        $this->albums[] = $albums;
    
        return $this;
    }

    /**
     * Remove albums
     *
     * @param \chlorius\ChloriusBundle\Entity\Album $albums
     */
    public function removeAlbum(\chlorius\ChloriusBundle\Entity\Album $albums)
    {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }
}