# AWS Lightsail Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/aws-lightsail-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/aws-lightsail-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/aws-lightsail-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/aws-lightsail-bundle)
[![License](https://img.shields.io/packagist/l/tourze/aws-lightsail-bundle.svg?style=flat-square)](LICENSE)
[![Downloads](https://img.shields.io/packagist/dt/tourze/aws-lightsail-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/aws-lightsail-bundle)
[![Tests](https://img.shields.io/badge/tests-314%20passed-brightgreen?style=flat-square)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen?style=flat-square)](#)

A comprehensive Symfony bundle for AWS Lightsail integration, providing an intuitive interface for managing 
Lightsail resources with EasyAdmin support.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Configuration](#configuration)
- [Supported Resources](#supported-resources)
  - [Console Commands](#console-commands)
  - [Access Admin Interface](#access-admin-interface)
  - [API Documentation](#api-documentation)
- [Advanced Configuration](#advanced-configuration)
- [Troubleshooting](#troubleshooting)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Complete AWS Lightsail Resource Management** - Manage all Lightsail resources from a single interface
- **EasyAdmin Integration** - Beautiful admin interface with minimal configuration
- **Multi-Credential Support** - Manage multiple AWS accounts from one application
- **Automatic Resource Synchronization** - Keep local database in sync with AWS
- **Console Commands** - Powerful CLI tools for automation and scripting
- **Comprehensive API** - Programmatic access to all Lightsail features
- **Secure Credential Management** - Encrypted storage of AWS credentials

## Requirements

- PHP 8.1 or higher
- Symfony 7.3 or higher
- Doctrine ORM 3.0 or higher
- AWS SDK for PHP 3.349.3
- EasyAdmin Bundle 4.0 or higher

## Installation

```bash
composer require tourze/aws-lightsail-bundle
```

### Step 1: Register the Bundle

```php
// config/bundles.php
return [
    // ...
    AwsLightsailBundle\AwsLightsailBundle::class => ['all' => true],
];
```

### Configuration

#### Step 2: Configure Environment Variables

Add the following to your `.env` or `.env.local` file:

```bash
AWS_ACCESS_KEY_ID=your-aws-access-key-id
AWS_SECRET_ACCESS_KEY=your-aws-secret-access-key
AWS_REGION=your-default-region # e.g., us-east-1
```

#### Create Database Tables

Run the database migration to create required tables:

```bash
php bin/console doctrine:schema:update --force
```

Or use migrations:

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## Quick Start

### Access Admin Interface

After installation, access the admin interface at:

```text
https://your-domain/admin/aws-lightsail
```

### Add AWS Credentials

The first step is to add AWS credentials in the admin interface to manage Lightsail resources.

### Example: Managing Instances

```php
<?php

use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Repository\InstanceRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

// Get the instance sync service
$instanceSyncService = $container->get(InstanceSyncService::class);

// Get the instance repository
$instanceRepository = $container->get(InstanceRepository::class);

// List all local instances
$instances = $instanceRepository->findAll();

// Sync instances from AWS to local database
$credential = $credentialRepository->findDefault();
$result = $instanceSyncService->batchSyncInstances($credential, $awsInstanceData);
```

## Supported Resources

The bundle supports management of all major Lightsail resources:

- **Instances** - Virtual private servers
- **Disks & Snapshots** - Block storage and backups
- **Static IPs** - Reserved IP addresses
- **Domains & DNS** - Domain management and DNS records
- **CDN Distributions** - Content delivery networks
- **Buckets** - Object storage
- **Databases** - Managed MySQL and PostgreSQL
- **Certificates** - SSL/TLS certificates
- **Load Balancers** - Application load balancers
- **Container Services** - Docker container deployment
- **Alarms** - Monitoring and alerting

## API Documentation

### Services

The bundle provides the following services for programmatic access:

```php
// Instance Sync Service
$instanceSyncService = $container->get(AwsLightsailBundle\Service\InstanceSyncService::class);

// Key Pair Sync Service
$keyPairSyncService = $container->get(AwsLightsailBundle\Service\KeyPairSyncService::class);

// Instance Data Updater
$instanceDataUpdater = $container->get(AwsLightsailBundle\Service\InstanceDataUpdater::class);

// Admin Menu Service
$adminMenuService = $container->get(AwsLightsailBundle\Service\AdminMenu::class);
```

### Working with Instance Synchronization

```php
// Sync instances from AWS
$credential = $credentialRepository->findDefault();
$result = $instanceSyncService->batchSyncInstances($credential, $awsInstanceData);

// Sync key pairs from AWS
$result = $keyPairSyncService->batchSyncKeyPairs($credential, $awsKeyPairData);

// Update instance data
$instanceDataUpdater->updateInstanceData($instance, $awsInstanceData);
```

## Console Commands

The bundle provides powerful console commands for managing Lightsail resources:

### Instance Control Command

```bash
# Start an instance
php bin/console aws:lightsail:instance:control start my-instance

# Stop an instance
php bin/console aws:lightsail:instance:control stop my-instance --force

# Reboot an instance
php bin/console aws:lightsail:instance:control reboot my-instance
```

**Parameters:**
- `operation`: Operation type (start/stop/reboot)
- `instance-name`: Instance name (optional, interactive selection if not provided)
- `--credential-id, -c`: AWS credential ID
- `--region, -r`: AWS region
- `--force, -f`: Force execution without confirmation

### Create Instance Command

```bash
# Create a basic instance
php bin/console aws:lightsail:instance:create my-new-instance

# Create an instance with specific parameters
php bin/console aws:lightsail:instance:create my-instance \
  --credential-id=123 \
  --region=us-east-1 \
  --blueprint=ubuntu_20_04 \
  --bundle=micro_2_0 \
  --availability-zone=us-east-1a \
  --key-pair-name=my-key \
  --tags="env=production,project=web"
```

**Parameters:**
- `name`: Instance name (required)
- `--credential-id, -c`: AWS credential ID
- `--region, -r`: AWS region
- `--blueprint, -b`: Blueprint ID (OS image)
- `--bundle`: Bundle ID (instance type)
- `--availability-zone, -z`: Availability zone
- `--key-pair-name, -k`: SSH key pair name
- `--tags, -t`: Tags (format: key1=value1,key2=value2)
- `--user-data, -u`: User data (startup script)

### Sync Instances Command

```bash
# Sync all credentials and regions
php bin/console aws:lightsail:instance:sync

# Sync specific credential
php bin/console aws:lightsail:instance:sync --credential-id=123

# Sync specific region
php bin/console aws:lightsail:instance:sync --region=us-east-1
```

**Parameters:**
- `--credential-id, -c`: AWS credential ID (optional, all credentials if not provided)
- `--region, -r`: Specific region (optional, all regions if not provided)

**Features:**
- Syncs instance information to local database
- Syncs SSH key pairs
- Removes locally stored resources that no longer exist in AWS
- Shows progress and statistics

## Advanced Configuration

### Custom Admin Routes

To customize the admin interface path, add to `config/routes.yaml`:

```yaml
aws_lightsail_admin:
  resource: '@AwsLightsailBundle/Controller/Admin/'
  type: annotation
  prefix: /custom-path/aws-lightsail
```

### Service Configuration

The bundle automatically configures all necessary services. You can override service definitions in your application's configuration if needed.

## Advanced Usage

### Multi-Region Support

The bundle supports managing resources across multiple AWS regions through the console commands:

```bash
# Sync instances from specific region
php bin/console aws:lightsail:instance:sync --region=us-west-2

# Create instance in specific region
php bin/console aws:lightsail:instance:create my-instance --region=us-west-2
```

### Multi-Credential Management

Manage resources from multiple AWS accounts:

```bash
# Use specific credential for operations
php bin/console aws:lightsail:instance:sync --credential-id=123

# Create instance with specific credential
php bin/console aws:lightsail:instance:create my-instance --credential-id=123
```

```php
// List all available credentials
$credentialRepository = $container->get(AwsLightsailBundle\Repository\AwsCredentialRepository::class);
$credentials = $credentialRepository->findAll();
```

## Troubleshooting

### Common Issues

1. **Missing AWS credentials**: Ensure AWS credentials are properly configured in the admin interface
2. **Permission errors**: Verify IAM user has necessary Lightsail permissions
3. **Region-specific resources**: Some resources may not be available in all regions

### Debug Mode

Enable debug logging for troubleshooting:

```yaml
# config/packages/monolog.yaml
monolog:
    channels: ['aws_lightsail']
    handlers:
        aws_lightsail:
            type: stream
            path: '%kernel.logs_dir%/aws_lightsail.log'
            level: debug
            channels: ['aws_lightsail']
```

## Security

### AWS Credentials Security

This bundle handles AWS credentials securely:

- **Environment Variables**: Store credentials in environment variables, not in code
- **IAM Best Practices**: Use IAM users with minimal required permissions
- **Credential Rotation**: Regularly rotate AWS access keys
- **Local Storage**: Credentials stored in database are encrypted

### Required AWS Permissions

Ensure your AWS IAM user has these permissions:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "lightsail:*"
            ],
            "Resource": "*"
        }
    ]
}
```

### Security Recommendations

1. **Use IAM Roles**: When running on AWS infrastructure, prefer IAM roles over access keys
2. **Environment Isolation**: Use different AWS credentials for different environments
3. **Access Logging**: Enable CloudTrail to monitor Lightsail API calls
4. **Network Security**: Restrict admin interface access to authorized networks

## Contributing

Contributions are welcome! Please ensure:

1. All tests pass
2. Code follows PSR-12 standards
3. New features include tests
4. Documentation is updated

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## References

- [AWS Lightsail API Reference](https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/Welcome.html)
- [AWS SDK for PHP - Lightsail](https://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Lightsail.LightsailClient.html)
- [Symfony Documentation](https://symfony.com/doc/current/index.html)
- [EasyAdmin Documentation](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html)
