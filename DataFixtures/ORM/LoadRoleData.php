<?php

namespace CustoMood\Bundle\AppBundle\DataFixtures\ORM;

use CustoMood\Bundle\AppBundle\Entity\Role;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Supported user roles of this app
     */
    const AVAILABLE_USER_ROLES = [
        'ROLE_USER',
        'ROLE_ADMIN'
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::AVAILABLE_USER_ROLES as $roleName) {
            $role = new Role();
            $role->setName($roleName);
            $this->addReference($roleName, $role);

            $manager->persist($role);
        }

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}