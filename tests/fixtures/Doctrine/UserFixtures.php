<?php

namespace Universal Reading\Tests\Fixtures\Doctrine;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Universal Reading\Entity\User;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $userAdmin = new User();
        $userAdmin->setName('Big boss');
        $userAdmin->setEmail('bigboss@universalreading.org');
        $userAdmin->setUsername('admin');
        $userAdmin->setPlainPassword('mypassword');
        $userAdmin->setEnabled(true);
        $userAdmin->addRole('ROLE_SUPER_ADMIN');

        $manager->persist($userAdmin);

        $this->addReference('admin-user', $userAdmin);

        $bobUser = new User();
        $bobUser->setName('Bobby');
        $bobUser->setEmail('bobby@universalreading.org');
        $bobUser->setUsername('bob');
        $bobUser->setPlainPassword('mypassword');
        $bobUser->setEnabled(true);

        $manager->persist($bobUser);

        $this->addReference('bob-user', $bobUser);

        $emptyUser = new User();
        $emptyUser->setName('Empty');
        $emptyUser->setEmail('empty@universalreading.org');
        $emptyUser->setUsername('empty');
        $emptyUser->setPlainPassword('mypassword');
        $emptyUser->setEnabled(true);

        $manager->persist($emptyUser);

        $this->addReference('empty-user', $emptyUser);

        $manager->flush();
    }
}
