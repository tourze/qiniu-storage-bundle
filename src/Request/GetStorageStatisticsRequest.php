<?php

namespace QiniuStorageBundle\Request;

/**
 * 获取存储统计信息请求
 */
class GetStorageStatisticsRequest extends QiniuApiRequest
{
    private string $bucketName;

    private string $begin;

    private string $end;

    private string $granularity;

    private string $type;

    public function __construct(string $bucketName, string $begin, string $end, string $granularity, string $type)
    {
        $this->bucketName = $bucketName;
        $this->begin = $begin;
        $this->end = $end;
        $this->granularity = $granularity;
        $this->type = $type;
    }

    protected function getPath(): string
    {
        return sprintf('/%s?bucket=%s&begin=%s&end=%s&g=%s',
            $this->type,
            $this->bucketName,
            $this->begin,
            $this->end,
            $this->granularity
        );
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
