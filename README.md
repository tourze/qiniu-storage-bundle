# Qiniu Storage Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/qiniu-storage-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/qiniu-storage-bundle)

A Symfony bundle for integrating Qiniu Cloud Storage services into your application.

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

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
