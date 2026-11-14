<?php

namespace Universal Reading\Tests\Functional\Controller;

use Symfony\Component\HttpFoundation\Response;
use Universal Reading\Tests\Functional\UniversalReadingTestCase;

class CaptchaControllerTest extends UniversalReadingTestCase
{
    public function testUnknownSessionKeyIsRejectedAnonymously(): void
    {
        $client = $this->getTestClient();

        $client->request('GET', '/_captcha/generate-captcha/unknown');

        $this->assertSame(Response::HTTP_PRECONDITION_REQUIRED, $client->getResponse()->getStatusCode());
        $this->assertSame('image/jpeg', $client->getResponse()->headers->get('Content-Type'));
    }
}
