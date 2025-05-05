<?php

namespace QiniuStorageBundle\Tests\Service;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Service\AuthService;
use QiniuStorageBundle\Service\QiniuAuthHelper;

class QiniuAuthHelperTest extends TestCase
{
    private QiniuAuthHelper $authHelper;
    private AuthService $authService;
    private Account $account;

    protected function setUp(): void
    {
        $this->authService = $this->createMock(AuthService::class);
        $this->authHelper = new QiniuAuthHelper($this->authService);
        $this->account = $this->createMock(Account::class);
    }

    public function testGenerateAuthHeaders_withValidParams_returnsExpectedHeaders(): void
    {
        $url = 'http://test.qiniuapi.com/buckets';
        $expectedAuthValue = 'Qiniu test-token:signature';

        // 设置AuthService模拟对象预期行为
        $this->authService->expects($this->once())
            ->method('createSignedUrl')
            ->with(
                $this->equalTo($this->account),
                $this->equalTo($url),
                $this->callback(function ($headers) {
                    return isset($headers['Content-Type']) &&
                        $headers['Content-Type'] === 'application/x-www-form-urlencoded' &&
                        isset($headers['X-Qiniu-Date']) &&
                        preg_match('/^\d{8}T\d{6}Z$/', $headers['X-Qiniu-Date']);
                })
            )
            ->willReturn($expectedAuthValue);

        $headers = $this->authHelper->generateAuthHeaders($this->account, $url);

        // 验证返回的头部
        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertEquals('application/x-www-form-urlencoded', $headers['Content-Type']);

        $this->assertArrayHasKey('X-Qiniu-Date', $headers);
        $this->assertMatchesRegularExpression('/^\d{8}T\d{6}Z$/', $headers['X-Qiniu-Date']);

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertEquals($expectedAuthValue, $headers['Authorization']);
    }

    public function testGenerateAuthHeaders_withComplexUrl_passesUrlUnmodified(): void
    {
        $url = 'http://test.qiniuapi.com/buckets?region=z0&type=public';

        // 验证URL被完整传递给createSignedUrl方法
        $this->authService->expects($this->once())
            ->method('createSignedUrl')
            ->with(
                $this->equalTo($this->account),
                $this->equalTo($url),
                $this->anything()
            )
            ->willReturn('Qiniu token:signature');

        $this->authHelper->generateAuthHeaders($this->account, $url);
    }

    public function testGenerateAuthHeaders_generatesCorrectDateFormat(): void
    {
        // 由于无法mock time()函数，这里我们只验证日期格式
        $capturedHeaders = null;
        $this->authService->expects($this->once())
            ->method('createSignedUrl')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(function ($headers) use (&$capturedHeaders) {
                    $capturedHeaders = $headers;
                    return true;
                })
            )
            ->willReturn('Qiniu token:signature');

        // 执行测试方法
        $this->authHelper->generateAuthHeaders($this->account, 'http://example.com');

        // 验证日期格式
        $this->assertNotNull($capturedHeaders);
        $this->assertArrayHasKey('X-Qiniu-Date', $capturedHeaders);
        $this->assertMatchesRegularExpression('/^\d{8}T\d{6}Z$/', $capturedHeaders['X-Qiniu-Date']);
    }
}
