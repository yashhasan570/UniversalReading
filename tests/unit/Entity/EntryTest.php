<?php

namespace Universal Reading\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use Universal Reading\Entity\Entry;
use Universal Reading\Entity\User;

class EntryTest extends TestCase
{
    public function testGetLanguage(): void
    {
        $entry = new Entry(new User());
        $languages = [
            'en_GB' => 'en-GB',
            'en_US' => 'en-US',
            'en-gb' => 'en-GB',
            'en-US' => 'en-US',
            'fr' => 'fr',
            'fr_FR' => 'fr-FR',
            'ja' => 'ja',
        ];
        foreach ($languages as $entryLang => $lang) {
            $entry->setLanguage($entryLang);
            $this->assertSame($lang, $entry->getHTMLLanguage());
        }
    }

    public function testSetPublishedByFiltersEmptyAuthors(): void
    {
        $entry = new Entry(new User());

        $entry->setPublishedBy(['', ' bob ', '  ', 'helen']);

        $this->assertSame(['bob', 'helen'], $entry->getPublishedBy());
    }

    public function testSetPublishedByStoresNullWhenOnlyEmptyAuthorsAreProvided(): void
    {
        $entry = new Entry(new User());

        $entry->setPublishedBy(['', '  ']);

        $this->assertNull($entry->getPublishedBy());
    }
}
