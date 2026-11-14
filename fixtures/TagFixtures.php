<?php

namespace Universal Reading\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Universal Reading\Entity\Tag;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tags = [
            'tag-universalreading' => 'universalreading',
            'tag-self-hosted' => 'self-hosted',
            'tag-privacy' => 'privacy',
            'tag-open-source' => 'open-source',
            'tag-ereader' => 'ereader',
            'tag-kobo' => 'kobo',
            'tag-migration' => 'migration',
            'tag-pocket' => 'pocket',
            'tag-omnivore' => 'omnivore',
            'tag-read-it-later' => 'read-it-later',
            'tag-tutorial' => 'tutorial',
            'tag-review' => 'review',
            'tag-howto' => 'howto',
            'tag-news' => 'news',
        ];

        foreach ($tags as $reference => $label) {
            $tag = new Tag();
            $tag->setLabel($label);

            $manager->persist($tag);

            $this->addReference($reference, $tag);
        }

        $manager->flush();
    }
}
