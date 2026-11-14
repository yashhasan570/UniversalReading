<?php

namespace Universal Reading\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Universal Reading\Entity\Entry;
use Universal Reading\Entity\User;
use Universal Reading\Helper\ContentProxy;

class EntryFixtures extends Fixture implements DependentFixtureInterface
{
    private const URLS = [
        'https://blog.narf.ssji.net/2025/01/17/manually-sending-document-contents-to-universalreading/',
        'https://nicolas.loeuillet.org/billets/2025/01/22/universalreadingit-a-8-ans-merci-omnivore/',
        'https://nicolas.loeuillet.org/billets/2025/01/28/que-sest-il-passe-en-2024/',
        'https://qmacro.org/blog/posts/2025/03/10/migrating-github-issue-based-url-bookmarks-to-universalreading/',
        'https://datanalytics.com/2025/05/08/universalreading/',
        'https://isedu.top/index.php/archives/300/',
        'https://www.justgeek.fr/alternatives-pocket-sauvegarde-articles-138495/',
        'https://lecrabeinfo.net/guides/pocket-4-alternatives-a-pocket-pour-sauvegarder-vos-articles-sur-le-web/',
        'https://www.ti-nuage.fr/blog/2025/05/de-pocket-a-universalreading/',
        'https://www.claudiuscoenen.de/2025/05/pocket-to-universalreading-hardcore-edition/',
        'https://planet.kde.org/matija-suklje-hook-2025-05-24-trying-out-koreader-and-universalreading-the-first-few-days-and-months/',
        'https://www.angelaambroz.com/posts/selfhosted_read_it_later/',
        'https://jqno.nl/post/2025/06/04/reading-blogs-on-my-kobo-ereader-via-universalreading/',
        'https://universalreading.org/news/20250627-apero-universalreading/',
        'https://manualdousuario.net/universalreading-alternativa-omnivore-pocket/',
        'https://nicolas.loeuillet.org/billets/2025/07/08/rip-pocket/',
        'https://universalreading.org/news/20250524-pocket-shutdown/',
        'https://linuxfr.org/news/pocket-est-mort-vive-universalreading',
        'https://justman.fr/on-the-web/fin-de-pocket-occasion-de-decouvrir-universalreading-alternative-libre',
        'https://goodtech.info/alternative-open-source-pocket-universalreading/',
        'https://blog.jonsdocs.org.uk/2025/07/27/switching-to-universalreading-pocket-replacement/',
        'https://hamatti.org/posts/universalreading-i-choose-you/',
        'https://blog.jonsdocs.org.uk/2025/08/18/more-experience-with-universalreading/',
        'https://www.xda-developers.com/universalreading-is-the-best-self-hosted-pocket-alternative/',
        'https://zenn.dev/ryobeam/articles/20251021-self-host-universalreading',
    ];

    // Entries archived by index (0-based)
    private const ARCHIVED_INDICES = [5, 6, 7, 8, 9, 10, 11, 12];

    // Entries starred by index (0-based)
    private const STARRED_INDICES = [13, 16, 20];

    public function __construct(
        private readonly ContentProxy $contentProxy,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $user = $this->getReference('dev-user', User::class);

        foreach (self::URLS as $index => $url) {
            $entry = new Entry($user);

            $this->contentProxy->updateEntry($entry, $url);

            if (\in_array($index, self::ARCHIVED_INDICES, true)) {
                $entry->setArchived(true);
                $entry->setArchivedAt(new \DateTime());
            }

            if (\in_array($index, self::STARRED_INDICES, true)) {
                $entry->setStarred(true);
                $entry->setStarredAt(new \DateTime());
            }

            $manager->persist($entry);
            $manager->flush();

            $this->addReference('entry-' . $index, $entry);
        }
    }

    public function getDependencies(): array
    {
        return [
            TaggingRuleFixtures::class,
            TagFixtures::class,
        ];
    }
}
