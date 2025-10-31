<?php

namespace QiniuStorageBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Request\GetBucketsRequest;

/**
 * @internal
 */
#[CoversClass(GetBucketsRequest::class)]
final class GetBucketsRequestTest extends RequestTestCase
{
    public function testCanInstantiate(): void
    {
        $request = new GetBucketsRequest();
        $this->assertInstanceOf(GetBucketsRequest::class, $request);
    }

    public function testGetPathReturnsCorrectPath(): void
    {
        $request = new GetBucketsRequest();
        $this->assertSame('/buckets', $request->getRequestPath());
    }

    public function testGetOptionsReturnsCorrectOptions(): void
    {
        $request = new GetBucketsRequest();
        $expectedOptions = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $this->assertSame($expectedOptions, $request->getRequestOptions());
    }
}
