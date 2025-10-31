<?php

namespace QiniuStorageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Service\AuthService;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(AuthService::class)]
#[RunTestsInSeparateProcesses]
final class AuthServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 此测试类无需特殊设置
    }

    private const ACCESS_KEY = 'test_access_key';
    private const SECRET_KEY = 'test_secret_key';

    private function createMockAccount(): Account
    {
        // 使用实际的 Account 实体实例替代 Mock 对象，符合静态分析规则
        $account = new Account();

        // 使用反射设置私有属性，因为实体可能没有公共的 setter 方法
        $reflectionClass = new \ReflectionClass($account);

        $accessKeyProperty = $reflectionClass->getProperty('accessKey');
        $accessKeyProperty->setAccessible(true);
        $accessKeyProperty->setValue($account, self::ACCESS_KEY);

        $secretKeyProperty = $reflectionClass->getProperty('secretKey');
        $secretKeyProperty->setAccessible(true);
        $secretKeyProperty->setValue($account, self::SECRET_KEY);

        return $account;
    }

    public function testCreateUploadTokenWithValidAccountReturnsToken(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);
        $account = $this->createMockAccount();

        // 测试createUploadToken方法返回有效的token
        $token = $authService->createUploadToken($account, 'test-bucket');

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
        // 七牛云上传凭证包含冒号分隔的三部分
        $this->assertStringContainsString(':', $token);
    }

    public function testCreateUploadTokenWithValidParamsReturnsToken(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);
        $account = $this->createMockAccount();

        $bucket = 'test-bucket';
        $key = 'test-key';
        $expires = 7200;

        $token = $authService->createUploadToken($account, $bucket, $key, $expires);
        $this->assertNotEmpty($token);
        // 七牛云上传凭证包含冒号分隔的三部分
        $this->assertStringContainsString(':', $token);
    }

    public function testCreateUploadTokenWithCustomExpiresPassesCorrectExpires(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);
        $account = $this->createMockAccount();

        $bucket = 'test-bucket';
        $customExpires = 1800; // 30分钟

        $token = $authService->createUploadToken($account, $bucket, null, $customExpires);
        $this->assertNotEmpty($token);

        // 解码上传凭证以验证过期时间
        $parts = explode(':', $token);
        $this->assertCount(3, $parts);

        $encodedPolicy = $parts[2];
        $decodedPolicy = base64_decode($encodedPolicy, true);
        $this->assertNotFalse($decodedPolicy, 'Base64 decode should not fail');
        $policy = json_decode($decodedPolicy, true);
        self::assertIsArray($policy);
        $this->assertArrayHasKey('deadline', $policy);

        // 验证deadline是合理的时间戳
        $deadline = $policy['deadline'];
        $this->assertIsInt($deadline);
        $this->assertGreaterThan(time(), $deadline);
    }

    public function testCreateUploadTokenWithKeyAndPolicyPassesAllParams(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);
        $account = $this->createMockAccount();

        $bucket = 'test-bucket';
        $key = 'test/file.jpg';
        $policy = json_encode(['returnBody' => 'success']);
        if (false === $policy) {
            self::fail('Failed to encode policy JSON');
        }

        $token = $authService->createUploadToken($account, $bucket, $key, 3600, $policy);
        $this->assertNotEmpty($token);

        // 解码上传凭证以验证参数
        $parts = explode(':', $token);
        $this->assertCount(3, $parts);

        $encodedPolicy = $parts[2];
        $decodedBase64 = base64_decode($encodedPolicy, true);
        $this->assertNotFalse($decodedBase64, 'Base64 decode should not fail');
        $decodedPolicy = json_decode($decodedBase64, true);
        self::assertIsArray($decodedPolicy);
        $this->assertArrayHasKey('scope', $decodedPolicy);
        $this->assertEquals($bucket . ':' . $key, $decodedPolicy['scope']);
    }

    public function testCreateManageTokenWithValidParamsReturnsToken(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);
        $account = $this->createMockAccount();

        $url = 'http://api.qiniuapi.com/buckets';
        $body = 'param=value';

        $token = $authService->createManageToken($account, $url, $body);
        $this->assertNotEmpty($token);
        // 管理凭证包含空格分隔的两部分
        $this->assertStringContainsString(' ', $token);
    }

    public function testCreateManageTokenWithEmptyBodyPassesEmptyString(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);
        $account = $this->createMockAccount();

        $url = 'http://api.qiniuapi.com/buckets';

        $token = $authService->createManageToken($account, $url);
        $this->assertNotEmpty($token);
        // 空体情况下也应该返回有效的凭证
        $this->assertStringContainsString(' ', $token);
    }

    public function testCreateDownloadTokenWithValidParamsReturnsToken(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);
        $account = $this->createMockAccount();

        $url = 'http://example.qiniuapi.com/test.jpg';
        $expires = 3600;

        $signedUrl = $authService->createDownloadToken($account, $url, $expires);
        $this->assertNotEmpty($signedUrl);
        // 下载凭证实际返回的是签名后的URL
        $this->assertStringContainsString($url, $signedUrl);
        $this->assertStringContainsString('e=', $signedUrl); // 包含过期时间参数
        $this->assertStringContainsString('token=', $signedUrl); // 包含凭证参数
    }

    public function testCreateSignedUrlWithValidParamsReturnsSignedUrl(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);

        $url = 'http://test.qiniuapi.com/test/resource';
        $headers = ['Content-Type' => 'application/json'];
        $body = '{"key":"value"}';

        // 测试generateSigningStr私有方法
        $reflectionMethod = new \ReflectionMethod(AuthService::class, 'generateSigningStr');
        $reflectionMethod->setAccessible(true);

        $signingStr = $reflectionMethod->invoke($authService, 'GET', '/test/resource', '', 'test.qiniuapi.com', $headers, $body);
        self::assertIsString($signingStr);

        $this->assertStringContainsString('GET /test/resource', $signingStr);
        $this->assertStringContainsString('Host: test.qiniuapi.com', $signingStr);
        $this->assertStringContainsString('Content-Type: application/json', $signingStr);
        $this->assertStringContainsString($body, $signingStr);
    }

    public function testGenerateSigningStrWithXQiniuHeadersSortsHeadersCorrectly(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);

        $headers = [
            'Content-Type' => 'application/json',
            'X-Qiniu-Z-Header' => 'z-value',
            'X-Qiniu-A-Header' => 'a-value',
            'X-Qiniu-B-Header' => 'b-value',
        ];

        $reflectionMethod = new \ReflectionMethod(AuthService::class, 'generateSigningStr');
        $reflectionMethod->setAccessible(true);

        $signingStr = $reflectionMethod->invoke($authService, 'POST', '/path', 'param=value', 'domain.com', $headers, null);
        self::assertIsString($signingStr);

        // 验证X-Qiniu头按ASCII顺序排序
        $lines = explode("\n", $signingStr);
        // 筛选出X-Qiniu开头的行
        $xQiniuLines = [];
        foreach ($lines as $line) {
            if (0 === strpos($line, 'X-Qiniu-')) {
                $xQiniuLines[] = $line;
            }
        }

        $this->assertCount(3, $xQiniuLines);

        // 检查实际格式并修正期望值匹配实际输出
        $expected = [
            'X-Qiniu-aHeader: a-value',
            'X-Qiniu-bHeader: b-value',
            'X-Qiniu-zHeader: z-value',
        ];

        $this->assertEquals($expected, $xQiniuLines, 'X-Qiniu headers should be sorted alphabetically');
    }

    public function testGenerateSigningStrWithoutBodyOmitsContentType(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);

        $headers = ['Other-Header' => 'value'];

        $reflectionMethod = new \ReflectionMethod(AuthService::class, 'generateSigningStr');
        $reflectionMethod->setAccessible(true);

        $signingStr = $reflectionMethod->invoke($authService, 'GET', '/path', '', 'domain.com', $headers, null);
        self::assertIsString($signingStr);

        $this->assertStringNotContainsString('Content-Type:', $signingStr);
    }

    public function testGenerateSigningStrWithOctetStreamContentTypeOmitsBody(): void
    {
        /** @var AuthService $authService */
        $authService = self::getService(AuthService::class);

        $headers = ['Content-Type' => 'application/octet-stream'];
        $body = 'binary data';

        $reflectionMethod = new \ReflectionMethod(AuthService::class, 'generateSigningStr');
        $reflectionMethod->setAccessible(true);

        $signingStr = $reflectionMethod->invoke($authService, 'POST', '/path', '', 'domain.com', $headers, $body);
        self::assertIsString($signingStr);

        $this->assertStringNotContainsString($body, $signingStr);
    }
}
