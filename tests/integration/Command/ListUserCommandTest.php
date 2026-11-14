<?php

namespace Universal Reading\Tests\Integration\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Universal Reading\Tests\Integration\UniversalReadingKernelTestCase;

class ListUserCommandTest extends UniversalReadingKernelTestCase
{
    public function testRunListUserCommand(): void
    {
        $application = $this->createApplication();

        $command = $application->find('universalreading:user:list');

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertStringContainsString('3/3 user(s) displayed.', $tester->getDisplay());
    }

    public function testRunListUserCommandWithLimit(): void
    {
        $application = $this->createApplication();

        $command = $application->find('universalreading:user:list');

        $tester = new CommandTester($command);
        $tester->execute([
            '--limit' => 2,
        ]);

        $this->assertStringContainsString('2/3 user(s) displayed.', $tester->getDisplay());
    }

    public function testRunListUserCommandWithSearch(): void
    {
        $application = $this->createApplication();

        $command = $application->find('universalreading:user:list');

        $tester = new CommandTester($command);
        $tester->execute([
            'search' => 'boss',
        ]);

        $this->assertStringContainsString('1/3 (filtered) user(s) displayed.', $tester->getDisplay());
    }

    public function testRunListUserCommandWithSearchAndLimit(): void
    {
        $application = $this->createApplication();

        $command = $application->find('universalreading:user:list');

        $tester = new CommandTester($command);
        $tester->execute([
            'search' => 'bo',
            '--limit' => 1,
        ]);

        $this->assertStringContainsString('1/3 (filtered) user(s) displayed.', $tester->getDisplay());
    }
}
