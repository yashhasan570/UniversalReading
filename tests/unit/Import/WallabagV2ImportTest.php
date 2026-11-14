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
use Universal Reading\Import\UniversalReadingV2Import;
use Universal Reading\Redis\Producer;
use Universal Reading\Repository\EntryRepository;

class UniversalReadingV2ImportTest extends TestCase
{
    protected $user;
    protected $em;
    protected $logHandler;
    protected $contentProxy;
    protected $tagsAssigner;
    protected $uow;

    public function testInit(): void
    {
        $universalreadingV2Import = $this->getUniversalReadingV2Import();

        $this->assertSame('universalreading v2', $universalreadingV2Import->getName());
        $this->assertNotEmpty($universalreadingV2Import->getUrl());
        $this->assertSame('import.universalreading_v2.description', $universalreadingV2Import->getDescription());
    }

    public function testImport(): void
    {
        $universalreadingV2Import = $this->getUniversalReadingV2Import(false, 2);
        $universalreadingV2Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->exactly(6))
            ->method('findByUrlAndUserId')
            ->will($this->onConsecutiveCalls(false, true, false));

        $this->em
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($entryRepo);

        $this->contentProxy
            ->expects($this->exactly(2))
            ->method('updateEntry');

        $res = $universalreadingV2Import->import();

        $this->assertTrue($res);
        $this->assertSame(['skipped' => 4, 'imported' => 2, 'queued' => 0], $universalreadingV2Import->getSummary());
    }

    public function testImportAndMarkAllAsRead(): void
    {
        $universalreadingV2Import = $this->getUniversalReadingV2Import(false, 2);
        $universalreadingV2Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2-read.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->exactly(2))
            ->method('findByUrlAndUserId')
            ->will($this->onConsecutiveCalls(false, false));

        $this->em
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($entryRepo);

        $this->contentProxy
            ->expects($this->exactly(2))
            ->method('updateEntry');

        // check that every entry persisted are archived
        $this->em
            ->expects($this->any())
            ->method('persist')
            ->with($this->callback(static fn ($persistedEntry) => (bool) $persistedEntry->isArchived()));

        $res = $universalreadingV2Import->setMarkAsRead(true)->import();

        $this->assertTrue($res);

        $this->assertSame(['skipped' => 0, 'imported' => 2, 'queued' => 0], $universalreadingV2Import->getSummary());
    }

    public function testImportWithRabbit(): void
    {
        $universalreadingV2Import = $this->getUniversalReadingV2Import();
        $universalreadingV2Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->never())
            ->method('findByUrlAndUserId');

        $this->em
            ->expects($this->never())
            ->method('getRepository');

        $this->contentProxy
            ->expects($this->never())
            ->method('updateEntry');

        $producer = $this->getMockBuilder(\OldSound\RabbitMqBundle\RabbitMq\Producer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $producer
            ->expects($this->exactly(6))
            ->method('publish');

        $universalreadingV2Import->setProducer($producer);

        $res = $universalreadingV2Import->setMarkAsRead(true)->import();

        $this->assertTrue($res);
        $this->assertSame(['skipped' => 0, 'imported' => 0, 'queued' => 6], $universalreadingV2Import->getSummary());
    }

    public function testImportWithRedis(): void
    {
        $universalreadingV2Import = $this->getUniversalReadingV2Import();
        $universalreadingV2Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->never())
            ->method('findByUrlAndUserId');

        $this->em
            ->expects($this->never())
            ->method('getRepository');

        $this->contentProxy
            ->expects($this->never())
            ->method('updateEntry');

        $factory = new RedisMockFactory();
        $redisMock = $factory->getAdapter(Client::class, true);

        $queue = new RedisQueue($redisMock, 'universalreading_v2');
        $producer = new Producer($queue);

        $universalreadingV2Import->setProducer($producer);

        $res = $universalreadingV2Import->setMarkAsRead(true)->import();

        $this->assertTrue($res);
        $this->assertSame(['skipped' => 0, 'imported' => 0, 'queued' => 6], $universalreadingV2Import->getSummary());

        $this->assertNotEmpty($redisMock->lpop('universalreading_v2'));
    }

    public function testImportBadFile(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV2Import();
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2.jsonx');

        $res = $universalreadingV1Import->import();

        $this->assertFalse($res);

        $records = $this->logHandler->getRecords();
        $this->assertStringContainsString('Universal ReadingImport: unable to read file', $records[0]['message']);
        $this->assertSame('ERROR', $records[0]['level_name']);
    }

    public function testImportUserNotDefined(): void
    {
        $universalreadingV1Import = $this->getUniversalReadingV2Import(true);
        $universalreadingV1Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2.json');

        $res = $universalreadingV1Import->import();

        $this->assertFalse($res);

        $records = $this->logHandler->getRecords();
        $this->assertStringContainsString('Universal ReadingImport: user is not defined', $records[0]['message']);
        $this->assertSame('ERROR', $records[0]['level_name']);
    }

    public function testImportEmptyFile(): void
    {
        $universalreadingV2Import = $this->getUniversalReadingV2Import();
        $universalreadingV2Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2-empty.json');

        $res = $universalreadingV2Import->import();

        $this->assertFalse($res);
        $this->assertSame(['skipped' => 0, 'imported' => 0, 'queued' => 0], $universalreadingV2Import->getSummary());
    }

    public function testImportWithExceptionFromGraby(): void
    {
        $universalreadingV2Import = $this->getUniversalReadingV2Import(false, 2);
        $universalreadingV2Import->setFilepath(__DIR__ . '/../../fixtures/Import/universalreading-v2.json');

        $entryRepo = $this->getMockBuilder(EntryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entryRepo->expects($this->exactly(6))
            ->method('findByUrlAndUserId')
            ->will($this->onConsecutiveCalls(false, true, false));

        $this->em
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($entryRepo);

        $this->contentProxy
            ->expects($this->exactly(2))
            ->method('updateEntry')
            ->will($this->throwException(new \Exception()));

        $res = $universalreadingV2Import->import();

        $this->assertTrue($res);
        $this->assertSame(['skipped' => 4, 'imported' => 2, 'queued' => 0], $universalreadingV2Import->getSummary());
    }

    private function getUniversalReadingV2Import($unsetUser = false, $dispatched = 0)
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

        $universalreading = new UniversalReadingV2Import($this->em, $this->contentProxy, $this->tagsAssigner, $dispatcher, $logger);

        if (false === $unsetUser) {
            $universalreading->setUser($this->user);
        }

        return $universalreading;
    }
}
