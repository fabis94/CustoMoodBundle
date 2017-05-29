<?php

namespace CustoMood\Bundle\AppBundle\DataFixtures\ORM;

use CustoMood\Bundle\AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /***
     * @var ContainerInterface
     */
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        // Create an admin account
        $admin = new User();
        $admin
            ->setUserName('admin')
            ->setEmail('admin@company.com');

        // Set password
        $password = $this->container->get('security.password_encoder')
            ->encodePassword($admin, 'password123');
        $admin->setPassword($password);

        // Add admin role
        $admin->addRole($this->getReference('ROLE_ADMIN'));

        $this->addReference('USER_ADMIN', $admin);
        $manager->persist($admin);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}