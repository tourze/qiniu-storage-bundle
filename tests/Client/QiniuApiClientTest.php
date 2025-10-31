<?php

declare(strict_types=1);

namespace QiniuStorageBundle\Tests\Client;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Client\QiniuApiClient;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Request\GetBucketsRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * QiniuApiClient 测试类
 *
 * @internal
 */
#[CoversClass(QiniuApiClient::class)]
#[RunTestsInSeparateProcesses]
final class QiniuApiClientTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 此测试类无需特殊设置
    }

    public function testApiClientCanBeRetrievedFromContainer(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');
        $this->assertInstanceOf(QiniuApiClient::class, $client);
    }

    public function testSetAndGetAccount(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');

        // 创建测试账户
        $account = new Account();
        $account->setAccessKey('test_access_key');
        $account->setSecretKey('test_secret_key');

        $client->setAccount($account);
        $retrievedAccount = $client->getAccount();

        $this->assertSame($account, $retrievedAccount);
        $this->assertEquals('test_access_key', $retrievedAccount->getAccessKey());
        $this->assertEquals('test_secret_key', $retrievedAccount->getSecretKey());
    }

    public function testGetBaseUrlReturnsCorrectUrl(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');
        $baseUrl = $client->getBaseUrl();

        $this->assertEquals('https://api.qiniuapi.com', $baseUrl);
    }

    public function testCreateQBoxAuthorization(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');

        // 创建测试账户
        $account = new Account();
        $account->setAccessKey('test_access_key');
        $account->setSecretKey('test_secret_key');
        $client->setAccount($account);

        $url = 'https://api.qiniuapi.com/buckets';
        $body = '';

        $auth = $client->createQBoxAuthorization($url, $body);

        $this->assertStringStartsWith('QBox test_access_key:', $auth);
        $this->assertStringContainsString(':', $auth);
    }

    public function testBase64UrlEncode(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');

        // 使用反射访问私有方法
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('base64UrlEncode');
        $method->setAccessible(true);

        $data = 'test data with + and /';
        $encoded = $method->invoke($client, $data);
        self::assertIsString($encoded);

        // URL安全的Base64编码应该不包含 + 和 /
        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);

        // 使用包含+和/的数据来测试替换功能
        $dataWithPlus = 'test>>>data';  // 这个会产生包含+的base64编码
        $dataWithSlash = chr(252);  // 这个会产生包含/的base64编码

        $encodedWithPlus = $method->invoke($client, $dataWithPlus);
        self::assertIsString($encodedWithPlus);
        $encodedWithSlash = $method->invoke($client, $dataWithSlash);
        self::assertIsString($encodedWithSlash);

        // 检查+被替换为-
        $this->assertStringNotContainsString('+', $encodedWithPlus);
        $this->assertStringContainsString('-', $encodedWithPlus);

        // 检查/被替换为_
        $this->assertStringNotContainsString('/', $encodedWithSlash);
        $this->assertStringContainsString('_', $encodedWithSlash);
    }

    public function testGetHttpClientInstance(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');
        $httpClient = $client->getHttpClientInstance();

        $this->assertInstanceOf(HttpClientInterface::class, $httpClient);
    }

    public function testGetRequestUrlWithQiniuApiRequest(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');
        $request = new GetBucketsRequest();

        // 使用反射访问受保护的方法
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('getRequestUrl');
        $method->setAccessible(true);

        $url = $method->invoke($client, $request);
        self::assertIsString($url);

        $this->assertStringStartsWith('https://api.qiniuapi.com', $url);
        $this->assertStringEndsWith('/buckets', $url);
    }

    public function testGetRequestMethod(): void
    {
        $client = self::getService('QiniuStorageBundle\Client\QiniuApiClient');
        $request = new GetBucketsRequest();

        // 使用反射访问受保护的方法
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('getRequestMethod');
        $method->setAccessible(true);

        $method = $method->invoke($client, $request);

        $this->assertEquals('GET', $method);
    }
}
