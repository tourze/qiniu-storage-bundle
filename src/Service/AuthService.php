<?php

namespace QiniuStorageBundle\Service;

use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Exception\QiniuApiException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

#[Autoconfigure(public: true)]
class AuthService
{
    /**
     * 根据账号配置创建上传凭证
     */
    public function createUploadToken(Account $account, string $bucket, ?string $key = null, int $expires = 3600, ?string $policy = null): string
    {
        // 构建上传凭证
        return $this->buildUploadToken($account, $bucket, $key, $expires, $policy);
    }

    /**
     * 构建上传凭证
     */
    private function buildUploadToken(Account $account, string $bucket, ?string $key, int $expires, ?string $policy): string
    {
        // 构建待签名的数据
        $scope = $bucket;
        if (null !== $key && '' !== $key) {
            $scope .= ':' . $key;
        }
        $data = [
            'scope' => $scope,
            'deadline' => time() + $expires,
        ];

        // 如果有策略，合并到数据中
        if (null !== $policy) {
            $policyArray = json_decode($policy, true);
            if (null === $policyArray) {
                throw new QiniuApiException('Invalid policy JSON: ' . $policy);
            }
            if (!is_array($policyArray)) {
                throw new QiniuApiException('Policy must be an array, got: ' . gettype($policyArray));
            }
            $data = array_merge($data, $policyArray);
        }

        // 编码为 JSON
        $jsonData = json_encode($data);
        if (false === $jsonData) {
            throw new QiniuApiException('Failed to encode data to JSON');
        }
        $encodedData = $this->base64UrlEncode($jsonData);

        // 生成签名
        $sign = $this->base64UrlEncode(hash_hmac('sha1', $encodedData, $account->getSecretKey(), true));

        // 返回完整的上传凭证
        return $account->getAccessKey() . ':' . $sign . ':' . $encodedData;
    }

    /**
     * 根据账号配置创建管理凭证
     */
    public function createManageToken(Account $account, string $url, string $body = ''): string
    {
        // 生成签名
        $sign = $this->signRequest($url, $body, $account->getSecretKey());

        // 返回 QBox 格式的管理凭证
        return 'QBox ' . $account->getAccessKey() . ':' . $sign;
    }

    /**
     * 根据账号配置创建下载凭证
     */
    public function createDownloadToken(Account $account, string $url, int $expires = 3600): string
    {
        // 解析 URL
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '/';
        $query = $parsedUrl['query'] ?? '';

        // 添加过期时间
        $expiresTime = time() + $expires;

        // 构建待签名的数据
        $signData = $path;
        if ('' !== $query) {
            $signData .= '?' . $query;
        }
        $signData .= "\n" . $expiresTime;

        // 生成签名
        $sign = $this->base64UrlEncode(hash_hmac('sha1', $signData, $account->getSecretKey(), true));

        // 构建最终的 URL
        $separator = '' === $query ? '?' : '&';

        return $url . $separator . 'e=' . $expiresTime . '&token=' . $account->getAccessKey() . ':' . $sign;
    }

    /**
     * 签名请求
     */
    private function signRequest(string $url, string $body, string $secretKey): string
    {
        // 解析 URL
        $parsedUrl = parse_url($url);
        if (false === $parsedUrl) {
            throw new QiniuApiException('Invalid URL: ' . $url);
        }
        $path = $parsedUrl['path'] ?? '/';
        $query = $parsedUrl['query'] ?? '';
        $host = $parsedUrl['host'] ?? '';
        if ('' === $host) {
            throw new QiniuApiException('Invalid URL host: ' . $url);
        }

        // 构建待签名的字符串
        $signingStr = $path;
        if ('' !== $query) {
            $signingStr .= '?' . $query;
        }
        $signingStr .= "\nHost: {$host}";
        $signingStr .= "\n\n";
        $signingStr .= $body;

        // 计算签名
        return $this->base64UrlEncode(hash_hmac('sha1', $signingStr, $secretKey, true));
    }

    /**
     * 生成签名的原始字符串
     *
     * @param array<string, string> $headers
     */
    private function generateSigningStr(string $method, string $path, string $query, string $host, array $headers = [], ?string $body = null): string
    {
        $signingStr = $this->buildRequestLine($method, $path, $query);
        $signingStr .= $this->buildHostLine($host);
        $signingStr .= $this->buildContentTypeLine($headers);
        $signingStr .= $this->buildQiniuHeaders($headers);
        $signingStr .= "\n\n";
        $signingStr .= $this->buildBodyPart($body, $headers);

        return $signingStr;
    }

    private function buildRequestLine(string $method, string $path, string $query): string
    {
        $line = $method . ' ' . $path;

        return '' !== $query ? $line . '?' . $query : $line;
    }

    private function buildHostLine(string $host): string
    {
        return "\nHost: {$host}";
    }

    /**
     * @param array<string, string> $headers
     */
    private function buildContentTypeLine(array $headers): string
    {
        return isset($headers['Content-Type']) ? "\nContent-Type: {$headers['Content-Type']}" : '';
    }

    /**
     * @param array<string, string> $headers
     */
    private function buildQiniuHeaders(array $headers): string
    {
        $qiniuHeaders = $this->extractQiniuHeaders($headers);
        ksort($qiniuHeaders);

        $result = '';
        foreach ($qiniuHeaders as $key => $value) {
            $result .= "\n{$key}: {$value}";
        }

        return $result;
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, string>
     */
    private function extractQiniuHeaders(array $headers): array
    {
        $qiniuHeaders = [];
        foreach ($headers as $key => $value) {
            if (str_starts_with($key, 'X-Qiniu-')) {
                $formattedKey = $this->formatQiniuHeaderKey($key);
                $qiniuHeaders[$formattedKey] = $value;
            }
        }

        return $qiniuHeaders;
    }

    private function formatQiniuHeaderKey(string $key): string
    {
        $result = preg_replace_callback('/^x-qiniu-|-([a-z])/', function ($matches) {
            return isset($matches[1]) ? strtoupper($matches[1]) : 'X-Qiniu-';
        }, strtolower($key));

        return $result ?? $key;
    }

    /**
     * @param array<string, string> $headers
     */
    private function buildBodyPart(?string $body, array $headers): string
    {
        if (null === $body) {
            return '';
        }

        if (!isset($headers['Content-Type'])) {
            return '';
        }

        return 'application/octet-stream' !== $headers['Content-Type'] ? $body : '';
    }

    /**
     * @param array<string, string> $headers
     */
    public function createSignedUrl(Account $account, string $url, array $headers = [], ?string $body = null): string
    {
        // 解析 URL 获取 path 和 query
        $parsedUrl = parse_url($url);
        if (false === $parsedUrl) {
            throw new QiniuApiException('Invalid URL: ' . $url);
        }
        $path = $parsedUrl['path'] ?? '/';
        $query = $parsedUrl['query'] ?? '';
        $host = $parsedUrl['host'] ?? '';
        if ('' === $host) {
            throw new QiniuApiException('Invalid URL host: ' . $url);
        }

        // 1. 生成待签名的原始字符串
        $signingStr = $this->generateSigningStr('GET', $path, $query, $host, $headers, $body);

        // 2. 计算 HMAC-SHA1 签名
        $sign = hash_hmac('sha1', $signingStr, $account->getSecretKey(), true);

        // 3. URL 安全的 Base64 编码
        $encodedSign = strtr(base64_encode($sign), '+/', '-_');

        // 4. 拼接最终的签名
        return 'Qiniu ' . $account->getAccessKey() . ':' . $encodedSign;
    }

    /**
     * URL 安全的 Base64 编码
     */
    private function base64UrlEncode(string $data): string
    {
        return strtr(base64_encode($data), '+/', '-_');
    }
}
