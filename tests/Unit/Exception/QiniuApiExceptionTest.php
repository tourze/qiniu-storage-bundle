<?php

namespace QiniuStorageBundle\Tests\Unit\Exception;

use PHPUnit\Framework\TestCase;
use QiniuStorageBundle\Exception\QiniuApiException;

class QiniuApiExceptionTest extends TestCase
{
    public function testExceptionIsRuntimeException(): void
    {
        $exception = new QiniuApiException();
        $this->assertInstanceOf(\RuntimeException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'API request failed';
        $exception = new QiniuApiException($message);
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithCode(): void
    {
        $message = 'API request failed';
        $code = 400;
        $exception = new QiniuApiException($message, $code);
        $this->assertSame($code, $exception->getCode());
    }

    public function testExceptionWithPreviousException(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new QiniuApiException('API request failed', 0, $previous);
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testThrowAndCatchException(): void
    {
        $this->expectException(QiniuApiException::class);
        $this->expectExceptionMessage('Test exception');
        
        throw new QiniuApiException('Test exception');
    }
}