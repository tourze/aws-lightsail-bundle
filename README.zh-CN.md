# AWS Lightsail Bundle

用于 Symfony 应用程序的 AWS Lightsail 集成包，提供易于使用的界面，用于管理 Lightsail 资源。

## 功能特点

- 完整的 AWS Lightsail 资源管理
- 基于 EasyAdmin 的直观管理界面
- 支持多 AWS 账户凭证管理
- 自动同步 AWS Lightsail 资源

## 安装

```bash
composer require tourze/aws-lightsail-bundle
```

### 注册 Bundle

```php
# config/bundles.php
return [
    // ...
    AwsLightsailBundle\AwsLightsailBundle::class => ['all' => true],
];
```

### 配置环境变量

在 `.env` 或 `.env.local` 文件中添加以下配置：

```
AWS_ACCESS_KEY_ID=你的AWS访问密钥ID
AWS_SECRET_ACCESS_KEY=你的AWS访问密钥
AWS_REGION=你的默认区域(例如: us-east-1)
```

### 创建数据库表

执行数据库迁移命令创建所需的表：

```bash
php bin/console doctrine:schema:update --force
```

或者使用迁移：

```bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```

## 使用方法

### 访问管理界面

安装完成后，访问以下 URL 进入管理界面：

```
https://你的网站/admin/aws-lightsail
```

### 添加 AWS 凭证

第一步是在管理界面中添加 AWS 凭证，这样才能管理 Lightsail 资源。

### 管理 Lightsail 资源

管理界面支持以下资源管理：

- 实例（虚拟服务器）
- 磁盘和快照
- 静态 IP
- 域名和 DNS 管理
- CDN 分发
- 存储桶
- 数据库
- 证书
- 负载均衡器
- 容器服务
- 告警和监控

## 编程 API

除了管理界面外，还可以通过 PHP 代码直接使用 API：

```php
// 获取实例服务
$instanceService = $container->get(AwsLightsailBundle\Service\InstanceService::class);

// 获取所有实例
$instances = $instanceService->getAllInstances();

// 启动实例
$instanceService->startInstance('instance-name');

// 停止实例
$instanceService->stopInstance('instance-name');

// 创建快照
$snapshotService = $container->get(AwsLightsailBundle\Service\SnapshotService::class);
$snapshotService->createSnapshot('instance-name', 'snapshot-name');
```

## 路由配置

如果需要自定义管理界面路径，可以在 `config/routes.yaml` 中添加：

```yaml
aws_lightsail_admin:
  resource: '@AwsLightsailBundle/Controller/Admin/'
  type: annotation
  prefix: /custom-path/aws-lightsail
```

## 支持的 Lightsail 功能

- 实例管理：创建、启动、停止、重启、删除
- 磁盘管理：创建、挂载、卸载、删除
- 快照管理：创建、删除、从快照恢复
- 网络管理：静态 IP、域名配置、负载均衡
- CDN 分发：创建、配置缓存行为、重置缓存
- 数据库管理：MySQL 和 PostgreSQL 数据库
- 存储桶管理：创建、配置访问权限、CORS 设置
- 监控告警：基于指标创建告警

## 参考文档

- [AWS Lightsail API 参考](https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/Welcome.html)
- [AWS SDK for PHP - Lightsail](https://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Lightsail.LightsailClient.html)
- [Symfony 文档](https://symfony.com/doc/current/index.html)
- [EasyAdmin 文档](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html)
