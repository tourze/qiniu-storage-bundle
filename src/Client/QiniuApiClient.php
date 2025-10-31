<?php

namespace QiniuStorageBundle\Client;

use HttpClientBundle\Client\ApiClient;
use HttpClientBundle\Request\RequestInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use QiniuStorageBundle\Entity\Account;
use QiniuStorageBundle\Exception\QiniuApiException;
use QiniuStorageBundle\Request\QiniuApiRequest;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;

/**
 * 七牛云 API 客户端
 */
#[WithMonologChannel(channel: 'qiniu_storage')]
#[AutoconfigureTag(name: 'monolog.logger', attributes: ['channel' => 'qiniu_api_client'])]
class QiniuApiClient extends ApiClient
{
    private Account $account;

    private LoggerInterface $logger;

    private HttpClientInterface $httpClient;

    private LockFactory $lockFactory;

    private CacheInterface $cache;

    private EventDispatcherInterface $eventDispatcher;

    private AsyncInsertService $asyncInsertService;

    public function __construct(
        LoggerInterface $logger,
        HttpClientInterface $httpClient,
        LockFactory $lockFactory,
        CacheInterface $cache,
        EventDispatcherInterface $eventDispatcher,
        AsyncInsertService $asyncInsertService,
    ) {
        $this->logger = $logger;
        $this->httpClient = $httpClient;
        $this->lockFactory = $lockFactory;
        $this->cache = $cache;
        $this->eventDispatcher = $eventDispatcher;
        $this->asyncInsertService = $asyncInsertService;
    }

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    protected function getLogger(): LoggerInterface
    {
        // 将通过依赖注入自动设置
        return $this->logger;
    }

    protected function getHttpClient(): HttpClientInterface
    {
        // 将通过依赖注入自动设置
        return $this->httpClient;
    }

    protected function getLockFactory(): LockFactory
    {
        // 将通过依赖注入自动设置
        return $this->lockFactory;
    }

    protected function getCache(): CacheInterface
    {
        // 将通过依赖注入自动设置
        return $this->cache;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        // 将通过依赖注入自动设置
        return $this->eventDispatcher;
    }

    protected function getAsyncInsertService(): AsyncInsertService
    {
        // 将通过依赖注入自动设置
        return $this->asyncInsertService;
    }

    protected function getRequestUrl(RequestInterface $request): string
    {
        if ($request instanceof QiniuApiRequest) {
            // 通过反射获取 protected 属性
            $reflection = new \ReflectionClass($request);
            $baseUrlProperty = $reflection->getProperty('baseUrl');
            $baseUrlProperty->setAccessible(true);
            $baseUrl = $baseUrlProperty->getValue($request);

            // 确保类型安全 - 避免直接将 mixed 转换为 string
            if (!is_string($baseUrl)) {
                throw new QiniuApiException('Invalid baseUrl type, expected string');
            }

            return $baseUrl . $request->getRequestPath();
        }

        // 对于其他类型的请求，抛出异常，因为我们只支持 QiniuApiRequest
        throw new QiniuApiException('Unsupported request type: ' . get_class($request));
    }

    protected function getRequestMethod(RequestInterface $request): string
    {
        return 'GET';
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getRequestOptions(RequestInterface $request): ?array
    {
        $options = $request->getRequestOptions() ?? [];

        // 添加 QBox 授权头
        if ($request instanceof QiniuApiRequest) {
            $url = $this->getRequestUrl($request);
            $body = $options['body'] ?? '';
            // 确保类型安全 - 避免直接将 mixed 转换为 string
            if (!is_string($body)) {
                $body = '';
            }

            // 确保 headers 数组存在且类型安全
            if (!isset($options['headers']) || !is_array($options['headers'])) {
                $options['headers'] = [];
            }
            $options['headers']['Authorization'] = $this->createQBoxAuthorization($url, $body);
        }

        // 确保返回类型正确，转换为期望的 array<string, mixed>
        if (count($options) === 0) {
            return null;
        }

        /** @var array<string, mixed> $typedOptions */
        $typedOptions = [];
        foreach ($options as $key => $value) {
            if (is_string($key)) {
                $typedOptions[$key] = $value;
            }
        }

        return $typedOptions;
    }

    protected function formatResponse(RequestInterface $request, ResponseInterface $response): mixed
    {
        $content = $response->getContent();
        $data = json_decode($content, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new QiniuApiException('Failed to decode JSON response: ' . json_last_error_msg());
        }

        return $data;
    }

    public function getBaseUrl(): string
    {
        return 'https://api.qiniuapi.com';
    }

    /**
     * 创建 QBox 格式的授权头
     */
    public function createQBoxAuthorization(string $url, string $body): string
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
        $sign = $this->base64UrlEncode(hash_hmac('sha1', $signingStr, $this->account->getSecretKey(), true));

        // 返回 QBox 格式的授权头
        return 'QBox ' . $this->account->getAccessKey() . ':' . $sign;
    }

    /**
     * URL 安全的 Base64 编码
     */
    private function base64UrlEncode(string $data): string
    {
        return strtr(base64_encode($data), '+/', '-_');
    }

    /**
     * 获取 HTTP 客户端实例
     */
    public function getHttpClientInstance(): HttpClientInterface
    {
        return $this->httpClient;
    }
}
