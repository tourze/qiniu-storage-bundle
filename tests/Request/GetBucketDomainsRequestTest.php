<?php

namespace QiniuStorageBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Request\GetBucketDomainsRequest;

/**
 * @internal
 */
#[CoversClass(GetBucketDomainsRequest::class)]
final class GetBucketDomainsRequestTest extends RequestTestCase
{
    public function testCanInstantiate(): void
    {
        $request = new GetBucketDomainsRequest('test-bucket');
        $this->assertInstanceOf(GetBucketDomainsRequest::class, $request);
    }

    public function testGetPathReturnsCorrectPath(): void
    {
        $request = new GetBucketDomainsRequest('test-bucket');
        $this->assertSame('/v6/domain/list?tbl=test-bucket', $request->getRequestPath());
    }

    public function testGetPathWithDifferentBucketNames(): void
    {
        $request1 = new GetBucketDomainsRequest('my-bucket-123');
        $request2 = new GetBucketDomainsRequest('another-bucket');

        $this->assertSame('/v6/domain/list?tbl=my-bucket-123', $request1->getRequestPath());
        $this->assertSame('/v6/domain/list?tbl=another-bucket', $request2->getRequestPath());
    }

    public function testGetOptionsReturnsCorrectOptions(): void
    {
        $request = new GetBucketDomainsRequest('test-bucket');
        $expectedOptions = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $this->assertSame($expectedOptions, $request->getRequestOptions());
    }

    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $request = new GetBucketDomainsRequest('test-bucket');
        $this->assertSame('/v6/domain/list?tbl=test-bucket', $request->getRequestPath());
    }

    public function testGetRequestOptionsReturnsCorrectOptions(): void
    {
        $request = new GetBucketDomainsRequest('test-bucket');
        $expectedOptions = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $this->assertSame($expectedOptions, $request->getRequestOptions());
    }
}
