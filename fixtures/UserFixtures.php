<?php

namespace Universal Reading\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Universal Reading\Entity\User;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $manager->getRepository(User::class)->findOneBy([], ['id' => 'ASC']);

        if (!$user) {
            $user = new User();
            $user->setUsername('universalreading');
            $user->setPlainPassword('universalreading');
            $user->setEmail('universalreading@universalreading.io');
            $user->setEnabled(true);
            $user->addRole('ROLE_SUPER_ADMIN');

            $manager->persist($user);
            $manager->flush();

            $this->dispatcher->dispatch(new UserEvent($user), FOSUserEvents::USER_CREATED);
        }

        $this->addReference('dev-user', $user);
    }
}
