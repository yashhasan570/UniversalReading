<?php

namespace Universal Reading\Tests\Functional\Controller\Import;

use Universal Reading\Tests\Functional\UniversalReadingTestCase;

class ImportControllerTest extends UniversalReadingTestCase
{
    public function testLogin(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', '/import/');

        $this->assertSame(302, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('login', $client->getResponse()->headers->get('location'));
    }

    public function testImportList(): void
    {
        $this->logInAs('admin');
        $client = $this->getTestClient();

        $crawler = $client->request('GET', '/import/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(13, $crawler->filter('.card-title')->count());
    }
}
