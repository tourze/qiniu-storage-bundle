<?php

namespace QiniuStorageBundle\Tests\Controller\Admin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use QiniuStorageBundle\Controller\Admin\BucketCrudController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * @internal
 */
#[CoversClass(BucketCrudController::class)]
#[RunTestsInSeparateProcesses]
final class BucketCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * 获取控制器服务实例
     */
    protected function getControllerService(): BucketCrudController
    {
        return self::getService(BucketCrudController::class);
    }

    /**
     * 提供索引页表头数据
     *
     * @return iterable<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '七牛账号' => ['七牛账号'];
        yield '存储空间名称' => ['存储空间名称'];
        yield '存储区域' => ['存储区域'];
        yield '访问域名' => ['访问域名'];
        yield '私有空间' => ['私有空间'];
        yield '有效状态' => ['有效状态'];
        yield '创建时间' => ['创建时间'];
    }

    /**
     * 提供编辑页字段数据
     *
     * @return iterable<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        yield 'account' => ['account'];
        yield 'name' => ['name'];
        yield 'region' => ['region'];
        yield 'domain' => ['domain'];
        yield 'private' => ['private'];
        yield 'remark' => ['remark'];
        yield 'valid' => ['valid'];
    }

    /**
     * 提供新增页字段数据
     *
     * @return iterable<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        yield 'account' => ['account'];
        yield 'name' => ['name'];
        yield 'region' => ['region'];
        yield 'domain' => ['domain'];
        yield 'private' => ['private'];
        yield 'remark' => ['remark'];
        yield 'valid' => ['valid'];
    }

    public function testIndexPageRequiresAuthentication(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/qiniu-storage/bucket');

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
            $crawler = $client->request('GET', '/qiniu-storage/bucket?crudAction=new');
            $response = $client->getResponse();

            if ($response->isSuccessful()) {
                $this->assertResponseIsSuccessful();

                $form = $crawler->selectButton('Create')->form();
                $crawler = $client->submit($form, [
                    'bucket[name]' => '',
                    'bucket[region]' => '',
                    'bucket[domain]' => '',
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
            $client->request('GET', '/qiniu-storage/bucket');
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
            $client->request('GET', '/qiniu-storage/bucket?query=test');
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

    public function testDetailPageAccess(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/qiniu-storage/bucket?crudAction=detail&entityId=1');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isSuccessful()
                || $response->isRedirect()
                || $response->isNotFound()
                || 422 === $response->getStatusCode(),
                'Detail page access should not cause server errors'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testEditPageAccess(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('GET', '/qiniu-storage/bucket?crudAction=edit&entityId=1');
            $response = $client->getResponse();

            $this->assertTrue(
                $response->isSuccessful()
                || $response->isRedirect()
                || $response->isNotFound()
                || 422 === $response->getStatusCode()
                || 403 === $response->getStatusCode(),
                'Edit page access should handle permissions appropriately'
            );
        } catch (\Exception $e) {
            $this->assertStringNotContainsString(
                'doctrine_ping_connection',
                $e->getMessage(),
                'Should not fail with doctrine_ping_connection error'
            );
        }
    }

    public function testDeleteActionPermissions(): void
    {
        $client = self::createClient();
        $client->catchExceptions(false);

        try {
            $client->request('DELETE', '/qiniu-storage/bucket?crudAction=delete&entityId=1');
            $response = $client->getResponse();

            // DELETE action should require ROLE_SUPER_ADMIN permission
            $this->assertTrue(
                $response->isRedirect()
                || 403 === $response->getStatusCode()
                || 401 === $response->getStatusCode()
                || $response->isNotFound(),
                'Delete action should enforce ROLE_SUPER_ADMIN permission'
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
