<?php

namespace Universal Reading\Tests\Unit\SiteConfig;

use PHPUnit\Framework\TestCase;
use Universal Reading\SiteConfig\ArraySiteConfigBuilder;
use Universal Reading\SiteConfig\SiteConfig;

class ArraySiteConfigBuilderTest extends TestCase
{
    public function testItReturnsSiteConfigThatExists(): void
    {
        $builder = new ArraySiteConfigBuilder(['example.com' => []]);
        $res = $builder->buildForHost('www.example.com');

        $this->assertInstanceOf(SiteConfig::class, $res);
    }

    public function testItReturnsFalseOnAHostThatDoesNotExist(): void
    {
        $builder = new ArraySiteConfigBuilder(['anotherexample.com' => []]);
        $res = $builder->buildForHost('example.com');

        $this->assertfalse($res);
    }
}
