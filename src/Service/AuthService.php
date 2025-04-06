<?php

namespace QiniuStorageBundle\Service;

use Qiniu\Auth;
use QiniuStorageBundle\Entity\Account;

class AuthService
{
    /**
     * 根据账号配置创建七牛云 Auth 实例
     */
    public function createAuth(Account $account): Auth
    {
        return new Auth($account->getAccessKey(), $account->getSecretKey());
    }

    /**
     * 根据账号配置创建上传凭证
     */
    public function createUploadToken(Account $account, string $bucket, ?string $key = null, int $expires = 3600, ?string $policy = null): string
    {
        $auth = $this->createAuth($account);
        return $auth->uploadToken($bucket, $key, $expires, $policy);
    }

    /**
     * 根据账号配置创建管理凭证
     */
    public function createManageToken(Account $account, string $url, string $body = ''): string
    {
        $auth = $this->createAuth($account);
        return $auth->sign($url) . ' ' . $auth->signRequest($url, $body);
    }

    /**
     * 根据账号配置创建下载凭证
     */
    public function createDownloadToken(Account $account, string $url, int $expires = 3600): string
    {
        $auth = $this->createAuth($account);
        return $auth->privateDownloadUrl($url, $expires);
    }

    /**
     * 根据账号配置创建签名URL
     */
    /**
     * 生成签名的原始字符串
     */
    private function generateSigningStr(string $method, string $path, string $query, string $host, array $headers = [], ?string $body = null): string
    {
        // 1. 基本请求信息
        $signingStr = $method . ' ' . $path;
        if ($query !== '') {
            $signingStr .= '?' . $query;
        }

        // 2. Host 信息
        $signingStr .= "\nHost: {$host}";

        // 3. Content-Type
        if (isset($headers['Content-Type'])) {
            $signingStr .= "\nContent-Type: {$headers['Content-Type']}";
        }

        // 4. X-Qiniu-* 头部
        $qiniuHeaders = [];
        foreach ($headers as $key => $value) {
            if (str_starts_with($key, 'X-Qiniu-')) {
                // 转换 key 格式：第一个字母和连字符后面的字母大写
                $formattedKey = preg_replace_callback('/^x-qiniu-|-([a-z])/', function($matches) {
                    return isset($matches[1]) ? strtoupper($matches[1]) : 'X-Qiniu-';
                }, strtolower($key));
                $qiniuHeaders[$formattedKey] = $value;
            }
        }

        // 按 ASCII 大小排序
        ksort($qiniuHeaders);
        foreach ($qiniuHeaders as $key => $value) {
            $signingStr .= "\n{$key}: {$value}";
        }

        // 5. 两个换行符
        $signingStr .= "\n\n";

        // 6. 请求体
        if ($body !== null && isset($headers['Content-Type']) && $headers['Content-Type'] !== 'application/octet-stream') {
            $signingStr .= $body;
        }

        return $signingStr;
    }

    public function createSignedUrl(Account $account, string $url, array $headers = [], ?string $body = null): string
    {
        // 解析 URL 获取 path 和 query
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        $query = $parsedUrl['query'] ?? '';
        $host = $parsedUrl['host'];

        // 1. 生成待签名的原始字符串
        $signingStr = $this->generateSigningStr('GET', $path, $query, $host, $headers, $body);

        // 2. 计算 HMAC-SHA1 签名
        $sign = hash_hmac('sha1', $signingStr, $account->getSecretKey(), true);

        // 3. URL 安全的 Base64 编码
        $encodedSign = strtr(base64_encode($sign), '+/', '-_');

        // 4. 拼接最终的签名
        return 'Qiniu ' . $account->getAccessKey() . ':' . $encodedSign;
    }
}
