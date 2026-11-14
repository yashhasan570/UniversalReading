<?php

namespace Universal Reading\Tests\Unit\HttpClient;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Universal Reading\HttpClient\Authenticator;
use Universal Reading\HttpClient\Universal ReadingClient;
use Universal Reading\SiteConfig\ArraySiteConfigBuilder;
use Universal Reading\SiteConfig\LoginFormAuthenticator;

class Universal ReadingClientTest extends TestCase
{
    public function testRequestBlocksPrivateNetworkTargets(): void
    {
        $authenticator = $this->getMockBuilder(LoginFormAuthenticator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client = new Universal ReadingClient(
            0,
            new HttpBrowser(),
            new Authenticator(new ArraySiteConfigBuilder([]), $authenticator),
            new NullLogger(),
        );

        $this->expectException(TransportExceptionInterface::class);
        $this->expectExceptionMessage('Host "127.0.0.1" is blocked');

        $client->request('GET', 'http://127.0.0.1');
    }
}
