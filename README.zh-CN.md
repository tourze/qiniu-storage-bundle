# 七牛云存储捆绑包

[English](README.md) | [中文](README.zh-CN.md)

[![最新版本](https://img.shields.io/packagist/v/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)
[![PHP 版本](https://img.shields.io/packagist/php-v/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)
[![许可证](https://img.shields.io/packagist/l/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://github.com/tourze/qiniu-storage-bundle/blob/master/LICENSE)
[![构建状态](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![代码覆盖率](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)
[![总下载量](https://img.shields.io/packagist/dt/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)

一个用于将七牛云存储服务集成到您的应用程序中的 Symfony 捆绑包。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [快速开始](#快速开始)
- [配置](#配置)
- [高级用法](#高级用法)
- [依赖关系](#依赖关系)
- [安全](#安全)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特性

- 轻松配置七牛云存储账号
- 存储空间（Bucket）管理和同步
- 为各种七牛云操作生成身份验证和令牌
- 全面的存储统计数据收集（小时级、天级、分钟级）
- 支持不同的存储类别（标准存储、低频存储、归档存储等）
- 自动化的统计数据同步定时任务

## 安装

```bash
composer require tourze/qiniu-storage-bundle
```

然后，在您的 `config/bundles.php` 中启用捆绑包：

```php
<?php

return [
    // ... 其他捆绑包
    QiniuStorageBundle\QiniuStorageBundle::class => ['all' => true],
];
```

## 快速开始

### 配置七牛云账号

使用管理界面添加您的七牛云凭证，或以编程方式创建：

```php
<?php

use QiniuStorageBundle\Entity\Account;

// 创建新的七牛云账号配置
$account = new Account();
$account->setName('我的七牛云账号')
    ->setAccessKey('你的访问密钥')
    ->setSecretKey('你的秘密密钥')
    ->setValid(true);

$entityManager->persist($account);
$entityManager->flush();
```

### 同步存储空间

运行提供的命令从您的七牛云账号同步存储空间：

```bash
php bin/console qiniu:sync-buckets
```

### 生成上传令牌

```php
<?php

use QiniuStorageBundle\Service\AuthService;

class MyController
{
    public function uploadAction(AuthService $authService)
    {
        $account = $this->getAccount(); // 获取您的账号实体
        $bucket = '您的存储空间名称';

        // 生成有效期为3600秒的上传令牌
        $uploadToken = $authService->createUploadToken($account, $bucket, null, 3600);

        // 将令牌返回给前端
        return $this->json(['uploadToken' => $uploadToken]);
    }
}
```

### 获取存储统计数据

您可以使用提供的命令同步存储统计数据：

```bash
# 同步小时级统计数据
php bin/console qiniu:sync-bucket-hour-statistics

# 同步天级统计数据
php bin/console qiniu:sync-bucket-day-statistics

# 同步分钟级统计数据
php bin/console qiniu:sync-bucket-minute-statistics
```

## 配置

### 环境变量

在 `.env` 文件中配置环境变量：

```env
# 可选：配置 API 请求的默认超时时间
QINIU_DEFAULT_TIMEOUT=30
```

### 捆绑包配置

在 `config/packages/qiniu_storage.yaml` 创建配置文件：

```yaml
qiniu_storage:
    default_timeout: 30
    retry_attempts: 3
```

## 依赖关系

此捆绑包需要以下依赖：

- `doctrine/orm` (^3.0) - 实体管理
- `symfony/http-client` (^7.3) - API 请求  
- `symfony/console` (^7.3) - 控制台命令
- `nesbot/carbon` (^2.72 || ^3) - 日期处理

## 安全

### 凭证管理

- 安全存储访问密钥和秘密密钥
- 使用环境变量进行敏感配置
- 定期轮换您的 API 凭证
- 在应用程序中实施适当的访问控制

### 速率限制

该捆绑包包含 API 请求的内置速率限制，以防止配额耗尽并确保
七牛云服务的公平使用。

## 高级用法

### 自定义身份验证

您可以扩展 AuthService 以实现自定义身份验证逻辑：

```php
<?php

use QiniuStorageBundle\Service\AuthService;

class CustomAuthService extends AuthService
{
    public function createCustomToken(Account $account, array $policy): string
    {
        // 自定义令牌生成逻辑
        return parent::createUploadToken($account, $policy['bucket'], $policy, 3600);
    }
}
```

### 存储统计 API

以编程方式访问详细的存储统计数据：

```php
<?php

use QiniuStorageBundle\Service\StorageStatisticsService;
use QiniuStorageBundle\Enum\TimeGranularity;
use QiniuStorageBundle\Entity\Bucket;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\ArrayInput;

class StatisticsController
{
    public function getStatistics(StorageStatisticsService $service, Bucket $bucket)
    {
        $begin = date('Ymd', strtotime('-7 days'));
        $end = date('Ymd');
        
        // 为控制台输出创建 SymfonyStyle 实例（getStandardStorage 方法必需）
        $io = new SymfonyStyle(new ArrayInput([]), new NullOutput());
        
        // 获取标准存储统计数据
        $standardStorage = $service->getStandardStorage(
            TimeGranularity::DAY, 
            $bucket, 
            $begin, 
            $end,
            $io
        );
        
        // 获取 GET 请求次数（io 参数是可选的）
        $getRequests = $service->getGetRequests(
            TimeGranularity::DAY,
            $bucket,
            $begin,
            $end,
            null
        );
        
        return $this->json([
            'standardStorage' => $standardStorage,
            'getRequests' => $getRequests
        ]);
    }
}
```

## 贡献

详情请参阅 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

MIT 许可证。详情请参阅 [License 文件](LICENSE)。
