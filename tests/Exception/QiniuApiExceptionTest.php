<?php

namespace QiniuStorageBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use QiniuStorageBundle\Exception\QiniuApiException;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(QiniuApiException::class)]
final class QiniuApiExceptionTest extends AbstractExceptionTestCase
{
    protected function getExceptionClass(): string
    {
        return QiniuApiException::class;
    }

    protected function getExpectedParentClass(): string
    {
        return \RuntimeException::class;
    }
}
