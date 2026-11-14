<?php

namespace Universal Reading\Tests\Unit\Import;

use PHPUnit\Framework\TestCase;
use Universal Reading\Import\ImportChain;
use Universal Reading\Import\ImportInterface;

class ImportChainTest extends TestCase
{
    public function testGetAll(): void
    {
        $import = $this->getMockBuilder(ImportInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $importChain = new ImportChain();
        $importChain->addImport($import, 'alias');

        $this->assertCount(1, $importChain->getAll());
        $this->assertSame($import, $importChain->getAll()['alias']);
    }
}
