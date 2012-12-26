<?php
namespace chlorius\ChloriusBundle\DataFixtures\ORM;

use chlorius\ChloriusBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('demo');
        $user->setEmail('demo@example.org');
        $user->setRoles(array('ROLE_USER'));
        $user->setEnabled(true);
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword('demodemo', $user->getSalt()));
        $manager->persist($user);

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setEmail('admin@example.org');
        $admin->setRoles(array('ROLE_USER', 'ROLE_ADMIN'));
        $admin->setEnabled(true);
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($admin);
        $admin->setPassword($encoder->encodePassword('adminadmin', $admin->getSalt()));
        $manager->persist($admin);

        $manager->flush();
    }
}
