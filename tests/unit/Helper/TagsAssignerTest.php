<?php

namespace Universal Reading\Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Universal Reading\Entity\Entry;
use Universal Reading\Entity\Tag;
use Universal Reading\Entity\User;
use Universal Reading\Helper\TagsAssigner;
use Universal Reading\Repository\TagRepository;

class TagsAssignerTest extends TestCase
{
    public function testAssignTagsWithArrayAndExtraSpaces(): void
    {
        $tagRepo = $this->getTagRepositoryMock();
        $tagsAssigner = new TagsAssigner($tagRepo);

        $entry = new Entry(new User());

        $tagsAssigner->assignTagsToEntry($entry, ['   tag1', 'tag2   ']);

        $this->assertCount(2, $entry->getTags());
        $this->assertSame('tag1', $entry->getTags()[0]->getLabel());
        $this->assertSame('tag2', $entry->getTags()[1]->getLabel());
    }

    public function testAssignTagsWithString(): void
    {
        $tagRepo = $this->getTagRepositoryMock();
        $tagsAssigner = new TagsAssigner($tagRepo);

        $entry = new Entry(new User());

        $tagsAssigner->assignTagsToEntry($entry, 'tag1, tag2');

        $this->assertCount(2, $entry->getTags());
        $this->assertSame('tag1', $entry->getTags()[0]->getLabel());
        $this->assertSame('tag2', $entry->getTags()[1]->getLabel());
    }

    public function testAssignTagsWithEmptyArray(): void
    {
        $tagRepo = $this->getTagRepositoryMock();
        $tagsAssigner = new TagsAssigner($tagRepo);

        $entry = new Entry(new User());

        $tagsAssigner->assignTagsToEntry($entry, []);

        $this->assertCount(0, $entry->getTags());
    }

    public function testAssignTagsWithEmptyString(): void
    {
        $tagRepo = $this->getTagRepositoryMock();
        $tagsAssigner = new TagsAssigner($tagRepo);

        $entry = new Entry(new User());

        $tagsAssigner->assignTagsToEntry($entry, '');

        $this->assertCount(0, $entry->getTags());
    }

    public function testAssignTagsAlreadyAssigned(): void
    {
        $tagRepo = $this->getTagRepositoryMock();
        $tagsAssigner = new TagsAssigner($tagRepo);

        $tagEntity = new Tag();
        $tagEntity->setLabel('tag1');

        $entry = new Entry(new User());
        $entry->addTag($tagEntity);

        $tagsAssigner->assignTagsToEntry($entry, 'tag1, tag2');

        $this->assertCount(2, $entry->getTags());
        $this->assertSame('tag1', $entry->getTags()[0]->getLabel());
        $this->assertSame('tag2', $entry->getTags()[1]->getLabel());
    }

    public function testAssignTagsNotFlushed(): void
    {
        $tagRepo = $this->getTagRepositoryMock();
        $tagRepo->expects($this->never())
            ->method('__call');

        $tagsAssigner = new TagsAssigner($tagRepo);

        $tagEntity = new Tag();
        $tagEntity->setLabel('tag1');

        $entry = new Entry(new User());

        $tagsAssigner->assignTagsToEntry($entry, 'tag1', [$tagEntity]);

        $this->assertCount(1, $entry->getTags());
        $this->assertSame('tag1', $entry->getTags()[0]->getLabel());
    }

    private function getTagRepositoryMock()
    {
        return $this->getMockBuilder(TagRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
