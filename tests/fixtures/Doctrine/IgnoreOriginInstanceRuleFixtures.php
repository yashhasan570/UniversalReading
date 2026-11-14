<?php

namespace Universal Reading\Tests\Fixtures\Doctrine;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Universal Reading\Entity\IgnoreOriginInstanceRule;

class IgnoreOriginInstanceRuleFixtures extends Fixture
{
    public function __construct(
        private readonly array $defaultIgnoreOriginInstanceRules,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->defaultIgnoreOriginInstanceRules as $ignoreOriginInstanceRule) {
            $newIgnoreOriginInstanceRule = $manager->getRepository(IgnoreOriginInstanceRule::class)->findOneBy(['rule' => $ignoreOriginInstanceRule['rule']]) ?? new IgnoreOriginInstanceRule();
            $newIgnoreOriginInstanceRule->setRule($ignoreOriginInstanceRule['rule']);
            $manager->persist($newIgnoreOriginInstanceRule);
        }

        $manager->flush();
    }
}
