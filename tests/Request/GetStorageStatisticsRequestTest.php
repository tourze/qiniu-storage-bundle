<?php

namespace QiniuStorageBundle\Tests\Request;

use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Request\GetStorageStatisticsRequest;

/**
 * @internal
 */
#[CoversClass(GetStorageStatisticsRequest::class)]
final class GetStorageStatisticsRequestTest extends RequestTestCase
{
    public function testCanInstantiate(): void
    {
        $request = new GetStorageStatisticsRequest('test-bucket', '2023-01-01', '2023-01-31', 'day', 'storage');
        $this->assertInstanceOf(GetStorageStatisticsRequest::class, $request);
    }

    public function testGetPathReturnsCorrectPath(): void
    {
        $request = new GetStorageStatisticsRequest('test-bucket', '2023-01-01', '2023-01-31', 'day', 'storage');
        $expectedPath = '/storage?bucket=test-bucket&begin=2023-01-01&end=2023-01-31&g=day';
        $this->assertSame($expectedPath, $request->getRequestPath());
    }

    public function testGetPathWithDifferentParameters(): void
    {
        $request1 = new GetStorageStatisticsRequest('my-bucket', '2023-02-01', '2023-02-28', 'hour', 'bandwidth');
        $request2 = new GetStorageStatisticsRequest('another-bucket', '2023-03-01', '2023-03-31', 'minute', 'requests');

        $this->assertSame('/bandwidth?bucket=my-bucket&begin=2023-02-01&end=2023-02-28&g=hour', $request1->getRequestPath());
        $this->assertSame('/requests?bucket=another-bucket&begin=2023-03-01&end=2023-03-31&g=minute', $request2->getRequestPath());
    }

    public function testGetOptionsReturnsCorrectOptions(): void
    {
        $request = new GetStorageStatisticsRequest('test-bucket', '2023-01-01', '2023-01-31', 'day', 'storage');
        $expectedOptions = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $this->assertSame($expectedOptions, $request->getRequestOptions());
    }

    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $request = new GetStorageStatisticsRequest('test-bucket', '2023-01-01', '2023-01-31', 'day', 'storage');
        $expectedPath = '/storage?bucket=test-bucket&begin=2023-01-01&end=2023-01-31&g=day';
        $this->assertSame($expectedPath, $request->getRequestPath());
    }

    public function testGetRequestOptionsReturnsCorrectOptions(): void
    {
        $request = new GetStorageStatisticsRequest('test-bucket', '2023-01-01', '2023-01-31', 'day', 'storage');
        $expectedOptions = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $this->assertSame($expectedOptions, $request->getRequestOptions());
    }
}
