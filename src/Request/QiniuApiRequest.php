<?php

namespace QiniuStorageBundle\Request;

use HttpClientBundle\Request\ApiRequest;

/**
 * 七牛云 API 请求基类
 */
abstract class QiniuApiRequest extends ApiRequest
{
    protected string $baseUrl = 'https://api.qiniuapi.com';

    public function getRequestPath(): string
    {
        return $this->getPath();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRequestOptions(): ?array
    {
        return $this->getOptions();
    }

    abstract protected function getPath(): string;

    /**
     * @return array<string, mixed>|null
     */
    abstract protected function getOptions(): ?array;
}
