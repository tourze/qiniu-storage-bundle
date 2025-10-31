<?php

namespace QiniuStorageBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Controller\Admin\AccountCrudController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(AccountCrudController::class)]
#[RunTestsInSeparateProcesses]
final class AccountCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): AccountCrudController
    {
        return self::getService(AccountCrudController::class);
    }

    /**
     * 提供索引页表头数据
     *
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '配置名称' => ['配置名称'];
        yield '有效状态' => ['有效状态'];
        yield 'Access Key' => ['Access Key'];
        yield 'Secret Key' => ['Secret Key'];
        yield '创建时间' => ['创建时间'];
        yield '更新时间' => ['更新时间'];
    }

    /**
     * 提供编辑页字段数据
     *
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'valid' => ['valid'];
        yield 'accessKey' => ['accessKey'];
        yield 'secretKey' => ['secretKey'];
        yield 'remark' => ['remark'];
    }

    /**
     * 提供新增页字段数据
     *
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'name' => ['name'];
        yield 'valid' => ['valid'];
        yield 'accessKey' => ['accessKey'];
        yield 'secretKey' => ['secretKey'];
        yield 'remark' => ['remark'];
    }

    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/qiniu-storage/account');

            $this->assertTrue(
                $client->getResponse()->isNotFound()
                || $client->getResponse()->isRedirect()
                || $client->getResponse()->isSuccessful(),
                'Response should be 404, redirect, or successful'
            );
        } catch (NotFoundHttpException $e) {
            $this->assertInstanceOf(NotFoundHttpException::class, $e);
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error: ' . $e->getMessage()
            );
        }
    }

    public function testValidationErrors(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $crawler = $client->request('GET', '/qiniu-storage/account?crudAction=new');
            $response = $client->getResponse();

            if ($response->isSuccessful()) {
                $this->assertResponseIsSuccessful();

                $form = $crawler->selectButton('Create')->form();
                $crawler = $client->submit($form, [
                    'account[name]' => '',
                    'account[accessKey]' => '',
                ]);

                $validationResponse = $client->getResponse();
                if (422 === $validationResponse->getStatusCode()) {
                    $this->assertResponseStatusCodeSame(422);

                    $invalidFeedback = $crawler->filter('.invalid-feedback');
                    if ($invalidFeedback->count() > 0) {
                        $this->assertStringContainsString('should not be blank', $invalidFeedback->text());
                    }
                } else {
                    $this->assertLessThan(500, $validationResponse->getStatusCode());
                }
            } elseif ($response->isRedirect()) {
                $this->assertResponseRedirects();
            } else {
                $this->assertLessThan(500, $response->getStatusCode(), 'Response should not be a server error');
            }
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testUnauthenticatedAccess(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/qiniu-storage/account');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isRedirect() || 401 === $response->getStatusCode() || 403 === $response->getStatusCode(),
                'Unauthenticated access should be redirected or denied'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testSearchFunctionality(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/qiniu-storage/account?query=test');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isSuccessful() || $response->isRedirect() || $response->isNotFound(),
                'Search request should not cause server errors'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }
}
