<?php

namespace QiniuStorageBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Service\QiniuAuthHelper;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(QiniuAuthHelper::class)]
#[RunTestsInSeparateProcesses]
final class QiniuAuthHelperTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 不需要特殊的设置
    }

    /**
     * 创建测试用的 Account 实例，替代 Mock 对象
     */
    private function createTestAccount(): Account
    {
        // 使用实际的 Account 实体实例替代 Mock 对象，符合静态分析规则
        $account = new Account();

        // 使用反射设置私有属性，因为实体可能没有公共的 setter 方法
        $reflectionClass = new \ReflectionClass($account);

        $accessKeyProperty = $reflectionClass->getProperty('accessKey');
        $accessKeyProperty->setAccessible(true);
        $accessKeyProperty->setValue($account, 'test_access_key');

        $secretKeyProperty = $reflectionClass->getProperty('secretKey');
        $secretKeyProperty->setAccessible(true);
        $secretKeyProperty->setValue($account, 'test_secret_key');

        return $account;
    }

    public function testConstructor(): void
    {
        /** @var QiniuAuthHelper $authHelper */
        $authHelper = self::getService(QiniuAuthHelper::class);

        $this->assertNotNull($authHelper);
    }

    public function testGenerateAuthHeadersReturnsArray(): void
    {
        /** @var QiniuAuthHelper $authHelper */
        $authHelper = self::getService(QiniuAuthHelper::class);

        // 使用实际的 Account 实体实例替代 Mock 对象，符合静态分析规则
        $account = $this->createTestAccount();

        $headers = $authHelper->generateAuthHeaders($account, 'http://example.com');

        $this->assertIsArray($headers);
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertArrayHasKey('X-Qiniu-Date', $headers);
        $this->assertArrayHasKey('Authorization', $headers);
    }

    public function testGenerateAuthHeadersContainsCorrectContentType(): void
    {
        /** @var QiniuAuthHelper $authHelper */
        $authHelper = self::getService(QiniuAuthHelper::class);

        // 使用实际的 Account 实体实例替代 Mock 对象，符合静态分析规则
        $account = $this->createTestAccount();

        $headers = $authHelper->generateAuthHeaders($account, 'http://example.com');

        $this->assertEquals('application/x-www-form-urlencoded', $headers['Content-Type']);
    }
}
