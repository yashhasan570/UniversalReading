<?php

namespace Universal Reading\Tests\Unit\Import;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use M6Web\Component\RedisMock\RedisMockFactory;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Simpleue\Queue\RedisQueue;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Universal Reading\Entity\Entry;
use Universal Reading\Entity\User;
use Universal Reading\Helper\ContentProxy;
use Universal Reading\Helper\TagsAssigner;
use Universal Reading\Import\UniversalReadingV1Import;
use Universal Reading\Redis\Producer;
use Universal Reading\Repository\EntryRepository;

class UniversalReadingV1ImportTest extends TestCase
{
    protected $user;
    protected $em;
    protected $logHandler;
    protected $contentProxy;
    protected $tagsAssigner;
    protected $uow;
    protected $fetchingErrorMessageTitle = 'No title found';
    protected $fetchingErrorMessage = 'universalreading can\'t retrieve contents for this article. Please <a href="http://doc.universalreading.org/en/master/user/errors_during_fetching.html#how-can-i-help-to-fix-that">troubleshoot this issue</a>.';

    public function testInit(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV1Import();

        $this->assertSame('universalreading v1', $universalreadingV1Import->getName());
        $this->assertNotEmpty($universalreadingV1Import->getUrl());
        $this->assertSame('import.universalreading_v1.description', $universalreadingV1Import->getDescription());
    }

    public function testImport(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV1Import(false, 1);
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v1.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->exactly(2))
            ->method('findByUrlAndUserId')
            ->will($this->onConsecutiveCalls(false, true, false, false));

        $this->em
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($entryRepo);

        $entry = $this->getMockBuilder(Entry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contentProxy
            ->expects($this->exactly(1))
            ->method('updateEntry');

        $res = $universalreadingV1Import->import();

        $this->assertTrue($res);
        $this->assertSame(['skipped' => 1, 'imported' => 1, 'queued' => 0], $universalreadingV1Import->getSummary());
    }

    public function testImportAndMarkAllAsRead(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV1Import(false, 3);
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v1-read.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->exactly(3))
            ->method('findByUrlAndUserId')
            ->will($this->onConsecutiveCalls(false, false, false));

        $this->em
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($entryRepo);

        $this->contentProxy
            ->expects($this->exactly(3))
            ->method('updateEntry');

        // check that every entry persisted are archived
        $this->em
            ->expects($this->any())
            ->method('persist')
            ->with($this->callback(static fn ($persistedEntry) => (bool) $persistedEntry->isArchived()));

        $res = $universalreadingV1Import->setMarkAsRead(true)->import();

        $this->assertTrue($res);

        $this->assertSame(['skipped' => 0, 'imported' => 3, 'queued' => 0], $universalreadingV1Import->getSummary());
    }

    public function testImportWithRabbit(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV1Import();
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v1.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->never())
            ->method('findByUrlAndUserId');

        $this->em
            ->expects($this->never())
            ->method('getRepository');

        $entry = $this->getMockBuilder(Entry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contentProxy
            ->expects($this->never())
            ->method('updateEntry');

        $producer = $this->getMockBuilder(\OldSound\RabbitMqBundle\RabbitMq\Producer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $producer
            ->expects($this->exactly(2))
            ->method('publish');

        $universalreadingV1Import->setProducer($producer);

        $res = $universalreadingV1Import->setMarkAsRead(true)->import();

        $this->assertTrue($res);
        $this->assertSame(['skipped' => 0, 'imported' => 0, 'queued' => 2], $universalreadingV1Import->getSummary());
    }

    public function testImportWithRedis(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV1Import();
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v1.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->never())
            ->method('findByUrlAndUserId');

        $this->em
            ->expects($this->never())
            ->method('getRepository');

        $entry = $this->getMockBuilder(Entry::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->contentProxy
            ->expects($this->never())
            ->method('updateEntry');

        $factory = new RedisMockFactory();
        $redisMock = $factory->getAdapter(Client::class, true);

        $queue = new RedisQueue($redisMock, 'universalreading_v1');
        $producer = new Producer($queue);

        $universalreadingV1Import->setProducer($producer);

        $res = $universalreadingV1Import->setMarkAsRead(true)->import();

        $this->assertTrue($res);
        $this->assertSame(['skipped' => 0, 'imported' => 0, 'queued' => 2], $universalreadingV1Import->getSummary());

        $this->assertNotEmpty($redisMock->lpop('universalreading_v1'));
    }

    public function testImportBadFile(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV1Import();
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v1.jsonx');

        $res = $universalreadingV1Import->import();

        $this->assertFalse($res);

        $records = $this->logHandler->getRecords();
        $this->assertStringContainsString('Universal ReadingImport: unable to read file', $records[0]['message']);
        $this->assertSame('ERROR', $records[0]['level_name']);
    }

    public function testImportUserNotDefined(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV1Import(true);
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v1.json');

        $res = $universalreadingV1Import->import();

        $this->assertFalse($res);

        $records = $this->logHandler->getRecords();
        $this->assertStringContainsString('Universal ReadingImport: user is not defined', $records[0]['message']);
        $this->assertSame('ERROR', $records[0]['level_name']);
    }

    private function getUniversalReadingV1Import($unsetUser = false, $dispatched = 0)
    {
        $this->user = new User();

        $this->em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->uow = $this->getMockBuilder(UnitOfWork::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->em
            ->expects($this->any())
            ->method('getUnitOfWork')
            ->willReturn($this->uow);

        $this->uow
            ->expects($this->any())
            ->method('getScheduledEntityInsertions')
            ->willReturn([]);

        $this->contentProxy = $this->getMockBuilder(ContentProxy::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->tagsAssigner = $this->getMockBuilder(TagsAssigner::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dispatcher
            ->expects($this->exactly($dispatched))
            ->method('dispatch');

        $this->logHandler = new TestHandler();
        $logger = new Logger('test', [$this->logHandler]);

        $universalreading = new UniversalReadingV1Import(
            $this->em,
            $this->contentProxy,
            $this->tagsAssigner,
            $dispatcher,
            $logger,
            $this->fetchingErrorMessageTitle,
            $this->fetchingErrorMessage
        );

        if (false === $unsetUser) {
            $universalreading->setUser($this->user);
        }

        return $universalreading;
    }
}
