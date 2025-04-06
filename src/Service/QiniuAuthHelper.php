<?php

namespace QiniuStorageBundle\Service;

use QiniuStorageBundle\Entity\Account;

/**
 * 七牛云认证辅助服务
 */
class QiniuAuthHelper
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    /**
     * 生成认证头部
     */
    public function generateAuthHeaders(Account $account, string $url): array
    {
        // 先生成需要的头部
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'X-Qiniu-Date' => gmdate('Ymd\THis\Z', time()),
        ];

        // 将头部信息也传给签名函数
        $headers['Authorization'] = $this->authService->createSignedUrl($account, $url, $headers);

        return $headers;
    }
}
