<?php

namespace QiniuStorageBundle\Request;

/**
 * 获取存储空间信息请求
 */
class GetBucketInfoRequest extends QiniuApiRequest
{
    private string $bucketName;

    public function __construct(string $bucketName)
    {
        $this->bucketName = $bucketName;
    }

    protected function getPath(): string
    {
        return sprintf('/v2/bucketInfo?bucket=%s', $this->bucketName);
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
