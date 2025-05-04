# AWS Lightsail Bundle

这个Symfony Bundle提供了与AWS Lightsail服务交互的能力，支持实例、域名、快照和网络等资源的管理。

## 安装

```bash
composer require tourze/aws-lightsail-bundle
```

## 功能特性

- 实例管理（创建、启动、停止、删除）
- 快照管理
- 域名管理
- 网络配置
- 分发服务管理
- 密钥对管理
- 实例指标数据获取

## 基本用法

### 配置Bundle

在你的Symfony项目中，需要注册该Bundle：

```php
// config/bundles.php
return [
    // ...
    AwsLightsailBundle\AwsLightsailBundle::class => ['all' => true],
];
```

### 使用服务

示例：创建实例

```php
use AwsLightsailBundle\Service\InstanceService;

class YourController
{
    public function createInstance(InstanceService $instanceService)
    {
        $result = $instanceService->createInstance(
            'your-instance-name',
            'us-east-1a',
            'amazon_linux_2',
            'nano_3_0',
            'your-key-pair',
            'your-access-key',
            'your-secret-key',
            'us-east-1'
        );
        
        return $this->json($result);
    }
}
```

## 测试

运行单元测试：

```bash
./vendor/bin/phpunit packages/aws-lightsail-bundle/tests
```

测试范围包括：
- AWS签名生成（AwsSignatureV4）
- 请求构建（各种Request类）
- API客户端（LightsailApiClient）
- 服务层（各种Service类）

## 贡献

欢迎提交Issue和Pull Request。
