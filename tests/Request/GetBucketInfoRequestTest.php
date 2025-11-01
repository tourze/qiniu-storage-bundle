<?php

namespace QiniuStorageBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Request\GetBucketInfoRequest;

/**
 * @internal
 */
#[CoversClass(GetBucketInfoRequest::class)]
final class GetBucketInfoRequestTest extends RequestTestCase
{
    public function testCanInstantiate(): void
    {
        $request = new GetBucketInfoRequest('test-bucket');
        $this->assertInstanceOf(GetBucketInfoRequest::class, $request);
    }

    public function testGetPathReturnsCorrectPath(): void
    {
        $request = new GetBucketInfoRequest('test-bucket');
        $this->assertSame('/v2/bucketInfo?bucket=test-bucket', $request->getRequestPath());
    }

    public function testGetPathWithDifferentBucketNames(): void
    {
        $request1 = new GetBucketInfoRequest('my-bucket-123');
        $request2 = new GetBucketInfoRequest('another-bucket');

        $this->assertSame('/v2/bucketInfo?bucket=my-bucket-123', $request1->getRequestPath());
        $this->assertSame('/v2/bucketInfo?bucket=another-bucket', $request2->getRequestPath());
    }

    public function testGetOptionsReturnsCorrectOptions(): void
    {
        $request = new GetBucketInfoRequest('test-bucket');
        $expectedOptions = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $this->assertSame($expectedOptions, $request->getRequestOptions());
    }

    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $request = new GetBucketInfoRequest('test-bucket');
        $this->assertSame('/v2/bucketInfo?bucket=test-bucket', $request->getRequestPath());
    }

    public function testGetRequestOptionsReturnsCorrectOptions(): void
    {
        $request = new GetBucketInfoRequest('test-bucket');
        $expectedOptions = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $this->assertSame($expectedOptions, $request->getRequestOptions());
    }
}
