# 七牛云存储捆绑包

[English](README.md) | [中文](README.zh-CN.md)

[![最新版本](https://img.shields.io/packagist/v/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)

一个用于将七牛云存储服务集成到您的应用程序中的 Symfony 捆绑包。

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

## 贡献

详情请参阅 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

MIT 许可证。详情请参阅 [License 文件](LICENSE)。
