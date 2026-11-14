<?php

namespace Universal Reading\Tests\Functional\Controller;

use Universal Reading\Tests\Functional\UniversalReadingTestCase;

class StaticControllerTest extends UniversalReadingTestCase
{
    public function testAbout(): void
    {
        $this->logInAs('admin');
        $client = $this->getTestClient();

        $client->request('GET', '/about');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testHowto(): void
    {
        $this->logInAs('admin');
        $client = $this->getTestClient();

        $client->request('GET', '/howto');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }
}
