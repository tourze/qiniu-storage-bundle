<?php

namespace QiniuStorageBundle\Request;

/**
 * 获取存储空间列表请求
 */
class GetBucketsRequest extends QiniuApiRequest
{
    protected function getPath(): string
    {
        return '/buckets';
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getOptions(): ?array
    {
        return [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ];
    }
}
