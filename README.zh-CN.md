# AWS Lightsail Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/aws-lightsail-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/aws-lightsail-bundle)
[![Latest Version](https://img.shields.io/packagist/v/tourze/aws-lightsail-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/aws-lightsail-bundle)
[![License](https://img.shields.io/packagist/l/tourze/aws-lightsail-bundle.svg?style=flat-square)](LICENSE)
[![Downloads](https://img.shields.io/packagist/dt/tourze/aws-lightsail-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/aws-lightsail-bundle)
[![Tests](https://img.shields.io/badge/tests-314%20passed-brightgreen?style=flat-square)](#)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen?style=flat-square)](#)

一个全面的 Symfony 包，用于 AWS Lightsail 集成，提供直观的界面来管理 Lightsail 资源，并支持 EasyAdmin。

## 目录

- [功能特点](#功能特点)
- [系统要求](#系统要求)
- [安装](#安装)
- [快速开始](#快速开始)
- [配置](#配置)
- [支持的资源](#支持的资源)
  - [控制台命令](#控制台命令)
  - [访问管理界面](#访问管理界面)
  - [API 文档](#api-文档)
- [高级配置](#高级配置)
- [故障排除](#故障排除)
- [安全](#安全)
- [贡献](#贡献)
- [许可证](#许可证)

## 功能特点

- **完整的 AWS Lightsail 资源管理** - 从单一界面管理所有 Lightsail 资源
- **EasyAdmin 集成** - 最小配置即可获得美观的管理界面
- **多凭证支持** - 从一个应用程序管理多个 AWS 账户
- **自动资源同步** - 保持本地数据库与 AWS 同步
- **控制台命令** - 强大的 CLI 工具用于自动化和脚本编写
- **全面的 API** - 编程访问所有 Lightsail 功能
- **安全凭证管理** - 加密存储 AWS 凭证

## 系统要求

- PHP 8.1 或更高版本
- Symfony 7.3 或更高版本
- Doctrine ORM 3.0 或更高版本
- AWS SDK for PHP 3.349.3
- EasyAdmin Bundle 4.0 或更高版本

## 安装

```bash
composer require tourze/aws-lightsail-bundle
```

### 步骤1: 注册 Bundle

```php
// config/bundles.php
return [
    // ...
    AwsLightsailBundle\AwsLightsailBundle::class => ['all' => true],
];
```

### 配置

#### 步骤2: 配置环境变量

在 `.env` 或 `.env.local` 文件中添加以下配置：

```bash
AWS_ACCESS_KEY_ID=你的AWS访问密钥ID
AWS_SECRET_ACCESS_KEY=你的AWS访问密钥
AWS_REGION=你的默认区域 # 例如: us-east-1
```

#### 创建数据库表

执行数据库迁移命令创建所需的表：

```bash
php bin/console doctrine:schema:update --force
```

或者使用迁移：

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## 快速开始

### 访问管理界面

安装完成后，访问以下 URL 进入管理界面：

```text
https://你的域名/admin/aws-lightsail
```

### 添加 AWS 凭证

第一步是在管理界面中添加 AWS 凭证，这样才能管理 Lightsail 资源。

### 示例：管理实例

```php
<?php

use AwsLightsailBundle\Service\InstanceSyncService;
use AwsLightsailBundle\Repository\InstanceRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

// 获取实例同步服务
$instanceSyncService = $container->get(InstanceSyncService::class);

// 获取实例仓库
$instanceRepository = $container->get(InstanceRepository::class);

// 列出所有本地实例
$instances = $instanceRepository->findAll();

// 从 AWS 同步实例到本地数据库
$credential = $credentialRepository->findDefault();
$result = $instanceSyncService->batchSyncInstances($credential, $awsInstanceData);
```

## 支持的资源

该 Bundle 支持管理所有主要的 Lightsail 资源：

- **实例** - 虚拟私有服务器
- **磁盘和快照** - 块存储和备份
- **静态 IP** - 保留的 IP 地址
- **域名和 DNS** - 域名管理和 DNS 记录
- **CDN 分发** - 内容分发网络
- **存储桶** - 对象存储
- **数据库** - 托管的 MySQL 和 PostgreSQL
- **证书** - SSL/TLS 证书
- **负载均衡器** - 应用负载均衡器
- **容器服务** - Docker 容器部署
- **告警** - 监控和告警

## API 文档

### 服务

该 Bundle 提供以下服务用于编程访问：

```php
// 实例同步服务
$instanceSyncService = $container->get(AwsLightsailBundle\Service\InstanceSyncService::class);

// 密钥对同步服务
$keyPairSyncService = $container->get(AwsLightsailBundle\Service\KeyPairSyncService::class);

// 实例数据更新器
$instanceDataUpdater = $container->get(AwsLightsailBundle\Service\InstanceDataUpdater::class);

// 管理菜单服务
$adminMenuService = $container->get(AwsLightsailBundle\Service\AdminMenu::class);
```

### 使用实例同步

```php
// 从 AWS 同步实例
$credential = $credentialRepository->findDefault();
$result = $instanceSyncService->batchSyncInstances($credential, $awsInstanceData);

// 从 AWS 同步密钥对
$result = $keyPairSyncService->batchSyncKeyPairs($credential, $awsKeyPairData);

// 更新实例数据
$instanceDataUpdater->updateInstanceData($instance, $awsInstanceData);
```

## 控制台命令

该 Bundle 提供了以下控制台命令用于管理 Lightsail 资源：

### 实例控制命令

```bash
# 启动实例
php bin/console aws:lightsail:instance:control start my-instance

# 停止实例
php bin/console aws:lightsail:instance:control stop my-instance --force

# 重启实例
php bin/console aws:lightsail:instance:control reboot my-instance
```

参数说明：
- `operation`: 操作类型（start/stop/reboot）
- `instance-name`: 实例名称（可选，不提供则交互式选择）
- `--credential-id, -c`: AWS 凭证 ID
- `--region, -r`: 区域
- `--force, -f`: 强制执行，不提示确认

### 创建实例命令

```bash
# 创建基本实例
php bin/console aws:lightsail:instance:create my-new-instance

# 创建实例并指定参数
php bin/console aws:lightsail:instance:create my-instance \
  --credential-id=123 \
  --region=us-east-1 \
  --blueprint=ubuntu_20_04 \
  --bundle=micro_2_0 \
  --availability-zone=us-east-1a \
  --key-pair-name=my-key \
  --tags="env=production,project=web"
```

参数说明：
- `name`: 实例名称（必需）
- `--credential-id, -c`: AWS 凭证 ID
- `--region, -r`: 区域
- `--blueprint, -b`: 蓝图 ID（操作系统镜像）
- `--bundle`: 套餐 ID（实例规格）
- `--availability-zone, -z`: 可用区
- `--key-pair-name, -k`: 密钥对名称
- `--tags, -t`: 标签（格式: key1=value1,key2=value2）
- `--user-data, -u`: 用户数据（启动脚本）

### 同步实例命令

```bash
# 同步所有凭证的所有区域
php bin/console aws:lightsail:instance:sync

# 同步指定凭证
php bin/console aws:lightsail:instance:sync --credential-id=123

# 同步指定区域
php bin/console aws:lightsail:instance:sync --region=us-east-1
```

参数说明：
- `--credential-id, -c`: AWS 凭证 ID（可选，不提供则使用所有凭证）
- `--region, -r`: 指定区域（可选，不提供则遍历所有区域）

该命令会：
- 同步实例信息到本地数据库
- 同步密钥对信息
- 清理远程已删除的资源
- 显示同步进度和统计信息

## 高级配置

### 自定义管理路由

要自定义管理界面路径，在 `config/routes.yaml` 中添加：

```yaml
aws_lightsail_admin:
  resource: '@AwsLightsailBundle/Controller/Admin/'
  type: annotation
  prefix: /custom-path/aws-lightsail
```

### 服务配置

Bundle 会自动配置所有必要的服务。如果需要，您可以在应用程序的配置中覆盖服务定义。

## 高级用法

### 多区域支持

Bundle 支持通过控制台命令跨多个 AWS 区域管理资源：

```bash
# 同步特定区域的实例
php bin/console aws:lightsail:instance:sync --region=us-west-2

# 在特定区域创建实例
php bin/console aws:lightsail:instance:create my-instance --region=us-west-2
```

### 多凭证管理

管理来自多个 AWS 账户的资源：

```bash
# 使用特定凭证进行操作
php bin/console aws:lightsail:instance:sync --credential-id=123

# 使用特定凭证创建实例
php bin/console aws:lightsail:instance:create my-instance --credential-id=123
```

```php
// 列出所有可用凭证
$credentialRepository = $container->get(AwsLightsailBundle\Repository\AwsCredentialRepository::class);
$credentials = $credentialRepository->findAll();
```

## 故障排除

### 常见问题

1. **缺少 AWS 凭证**：确保在管理界面中正确配置了 AWS 凭证
2. **权限错误**：验证 IAM 用户具有必要的 Lightsail 权限
3. **区域特定资源**：某些资源可能并非在所有区域都可用

### 调试模式

启用调试日志进行故障排除：

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

## 安全

### AWS 凭证安全

此 Bundle 安全地处理 AWS 凭证：

- **环境变量**：将凭证存储在环境变量中，而非代码中
- **IAM 最佳实践**：使用具有最小必需权限的 IAM 用户
- **凭证轮换**：定期轮换 AWS 访问密钥
- **本地存储**：存储在数据库中的凭证已加密

### 必需的 AWS 权限

确保您的 AWS IAM 用户具有以下权限：

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

### 安全建议

1. **使用 IAM 角色**：在 AWS 基础设施上运行时，优先使用 IAM 角色而非访问密钥
2. **环境隔离**：为不同环境使用不同的 AWS 凭证
3. **访问日志**：启用 CloudTrail 监控 Lightsail API 调用
4. **网络安全**：限制管理界面仅对授权网络开放

## 贡献

欢迎贡献！请确保：

1. 所有测试通过
2. 代码遵循 PSR-12 标准
3. 新功能包含测试
4. 文档已更新

## 许可证

MIT 许可证（MIT）。请参阅[许可证文件](LICENSE)了解更多信息。

## 参考文档

- [AWS Lightsail API 参考](https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/Welcome.html)
- [AWS SDK for PHP - Lightsail](https://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Lightsail.LightsailClient.html)
- [Symfony 文档](https://symfony.com/doc/current/index.html)
- [EasyAdmin 文档](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html)
