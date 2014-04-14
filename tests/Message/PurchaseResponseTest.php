<?php

namespace Omnipay\Rabobank\Message;

use Omnipay\Tests\TestCase;

class PurchaseResponseTest extends TestCase
{
    public function testSuccess()
    {
        $data = array('foo' => 'bar');
        $request = $this->getMockRequest();
        $request->shouldReceive('getEndpoint')->once()
            ->andReturn('https://example.com/api');

        $response = new PurchaseResponse($request, $data);

        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame('https://example.com/api', $response->getRedirectUrl());
        $this->assertSame('POST', $response->getRedirectMethod());
        $this->assertSame($data, $response->getRedirectData());
    }
}
