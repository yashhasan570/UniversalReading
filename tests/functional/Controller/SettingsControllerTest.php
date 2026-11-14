<?php

namespace Universal Reading\Tests\Functional\Controller;

use Universal Reading\Tests\Functional\UniversalReadingTestCase;

/**
 * The controller `SettingsController` does not exist.
 * This test cover security against the internal settings page managed by CraueConfigBundle.
 */
class SettingsControllerTest extends UniversalReadingTestCase
{
    public function testSettingsWithAdmin(): void
    {
        $this->logInAs('admin');
        $client = $this->getTestClient();

        $crawler = $client->request('GET', '/settings');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testSettingsWithNormalUser(): void
    {
        $this->logInAs('bob');
        $client = $this->getTestClient();

        $crawler = $client->request('GET', '/settings');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
}
