<?php

namespace QiniuStorageBundle\Request;

/**
 * 获取域名列表请求
 */
class GetBucketDomainsRequest extends QiniuApiRequest
{
    private string $bucketName;

    public function __construct(string $bucketName)
    {
        $this->bucketName = $bucketName;
    }

    protected function getPath(): string
    {
        return sprintf('/v6/domain/list?tbl=%s', $this->bucketName);
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
