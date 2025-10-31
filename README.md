# Qiniu Storage Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)
[![PHP Version](https://img.shields.io/packagist/php-v/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)
[![License](https://img.shields.io/packagist/l/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://github.com/tourze/qiniu-storage-bundle/blob/master/LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?style=flat-square)](https://github.com/tourze/php-monorepo/actions)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)

A Symfony bundle for integrating Qiniu Cloud Storage services into your application.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Advanced Usage](#advanced-usage)
- [Dependencies](#dependencies)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Features

- Easy configuration of Qiniu Cloud Storage accounts
- Bucket management and synchronization
- Authentication and token generation for various Qiniu operations
- Comprehensive storage statistics collection (hourly, daily, minute-level)
- Support for different storage classes (standard, infrequent access, archive, etc.)
- Automatic cron tasks for statistics synchronization

## Installation

```bash
composer require tourze/qiniu-storage-bundle
```

Then, enable the bundle in your `config/bundles.php`:

```php
<?php

return [
    // ... other bundles
    QiniuStorageBundle\QiniuStorageBundle::class => ['all' => true],
];
```

## Quick Start

### Configure Qiniu Account

Use the admin interface to add your Qiniu Cloud credentials or create them programmatically:

```php
<?php

use QiniuStorageBundle\Entity\Account;

// Create a new Qiniu account configuration
$account = new Account();
$account->setName('My Qiniu Account')
    ->setAccessKey('your-access-key')
    ->setSecretKey('your-secret-key')
    ->setValid(true);

$entityManager->persist($account);
$entityManager->flush();
```

### Synchronize Buckets

Run the provided command to synchronize buckets from your Qiniu account:

```bash
php bin/console qiniu:sync-buckets
```

### Generate Upload Token

```php
<?php

use QiniuStorageBundle\Service\AuthService;

class MyController
{
    public function uploadAction(AuthService $authService)
    {
        $account = $this->getAccount(); // Get your account entity
        $bucket = 'your-bucket-name';

        // Generate an upload token valid for 3600 seconds
        $uploadToken = $authService->createUploadToken($account, $bucket, null, 3600);

        // Return the token to your frontend
        return $this->json(['uploadToken' => $uploadToken]);
    }
}
```

### Fetch Storage Statistics

You can use the provided commands to synchronize storage statistics:

```bash
# Synchronize hourly statistics
php bin/console qiniu:sync-bucket-hour-statistics

# Synchronize daily statistics
php bin/console qiniu:sync-bucket-day-statistics

# Synchronize minute-level statistics
php bin/console qiniu:sync-bucket-minute-statistics
```

## Configuration

### Environment Variables

Configure your environment variables in `.env`:

```env
# Optional: Configure default timeout for API requests
QINIU_DEFAULT_TIMEOUT=30
```

### Bundle Configuration

Create a configuration file at `config/packages/qiniu_storage.yaml`:

```yaml
qiniu_storage:
    default_timeout: 30
    retry_attempts: 3
```

## Dependencies

This bundle requires the following packages:

- `doctrine/orm` (^3.0) - For entity management
- `symfony/http-client` (^7.3) - For API requests  
- `symfony/console` (^7.3) - For console commands
- `nesbot/carbon` (^2.72 || ^3) - For date handling

## Security

### Credentials Management

- Store Access Keys and Secret Keys securely
- Use environment variables for sensitive configuration
- Regularly rotate your API credentials
- Implement proper access controls in your application

### Rate Limiting

The bundle includes built-in rate limiting for API requests to prevent
quota exhaustion and ensure fair usage of Qiniu Cloud services.

## Advanced Usage

### Custom Authentication

You can extend the AuthService for custom authentication logic:

```php
<?php

use QiniuStorageBundle\Service\AuthService;

class CustomAuthService extends AuthService
{
    public function createCustomToken(Account $account, array $policy): string
    {
        // Custom token generation logic
        return parent::createUploadToken($account, $policy['bucket'], $policy, 3600);
    }
}
```

### Storage Statistics API

Access detailed storage statistics programmatically:

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
        
        // Create a SymfonyStyle instance for console output (required for getStandardStorage)
        $io = new SymfonyStyle(new ArrayInput([]), new NullOutput());
        
        // Get standard storage statistics
        $standardStorage = $service->getStandardStorage(
            TimeGranularity::DAY, 
            $bucket, 
            $begin, 
            $end,
            $io
        );
        
        // Get GET requests count (io parameter is optional)
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

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
