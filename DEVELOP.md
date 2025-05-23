# AWS Lightsail Bundle 开发计划

## 项目概述 🔍

AWS Lightsail Bundle 是一个 Symfony Bundle，旨在提供对 AWS Lightsail 服务的便捷访问和管理。Lightsail 是 AWS 提供的一个简化版 VPS 服务，包括虚拟服务器、容器服务、存储桶、托管数据库、基于 SSD 的块存储、静态 IP 地址、负载均衡器、CDN 分发、DNS 管理和资源快照等功能。

本 Bundle 将封装 AWS PHP SDK 中的 Lightsail 客户端，并提供易于使用的接口以便在 Symfony 应用程序中集成和管理 Lightsail 资源。

> **注意**：本开发计划中提到的所有功能（包括实例、数据库、CDN 分发、存储桶等）都是 Lightsail 服务的一部分，而不是独立的 AWS 服务。例如，Lightsail Distribution 是 Lightsail 提供的 CDN 功能，区别于 AWS CloudFront 服务。

## 开发环境设置 🔴

1. **安装开发依赖** 🔴

   ```bash
   composer install --prefer-dist --no-progress
   ```

2. **配置环境变量** 🔴
   - 创建 `.env.local` 文件并配置以下必要的环境变量：

   ```
   AWS_ACCESS_KEY_ID=your_access_key
   AWS_SECRET_ACCESS_KEY=your_secret_key
   AWS_REGION=desired_region
   ```

3. **测试设置** 🔴

   ```bash
   ./vendor/bin/phpunit tests
   ```

## 项目结构

### 已有的基础结构 ✅

```
src/
├── AwsLightsailBundle.php           # Bundle 入口类 ✅
├── DependencyInjection/             # 依赖注入相关代码 ✅
│   └── AwsLightsailExtension.php    # 加载服务配置 ✅
└── Resources/
    └── config/
        └── services.yaml            # 服务配置 ✅
```

### 待开发的部分

#### Command 命令行工具 🔴

- `AwsCredentialCommand.php` 🔴 - AWS 凭证管理命令
- `InstanceCommand.php` 🔴 - 实例管理命令
- `SnapshotCommand.php` 🔴 - 快照管理命令
- `DomainCommand.php` 🔴 - 域名管理命令
- `DistributionCommand.php` 🔴 - CDN 分发管理命令
- `KeyPairCommand.php` 🔴 - 密钥对管理命令
- `BucketCommand.php` 🔴 - 存储桶管理命令
- `DatabaseCommand.php` 🔴 - 数据库管理命令
- `LoadBalancerCommand.php` 🔴 - 负载均衡器管理命令

#### Entity 实体类 🔴

- `AwsCredential.php` ✅ - AWS 凭证实体
- `Instance.php` ✅ - 实例实体 (来源: GetInstance, GetInstances API)
- `Snapshot.php` ✅ - 快照实体 (来源: GetInstanceSnapshot, GetInstanceSnapshots API)
- `Disk.php` ✅ - 磁盘实体 (来源: GetDisk, GetDisks API)
- `DiskSnapshot.php` ✅ - 磁盘快照实体 (来源: GetDiskSnapshot, GetDiskSnapshots API)
- `StaticIp.php` ✅ - 静态 IP 实体 (来源: GetStaticIp, GetStaticIps API)
- `Domain.php` ✅ - 域名实体 (来源: GetDomain, GetDomains API)
- `DomainEntry.php` ✅ - 域名记录实体 (来源: GetDomain API 中的 domainEntries 属性)
- `Distribution.php` ✅ - Lightsail CDN 分发实体 (来源: GetDistribution, GetDistributions API)
- `Certificate.php` ✅ - 证书实体 (来源: GetCertificates API)
- `KeyPair.php` ✅ - 密钥对实体 (来源: GetKeyPair, GetKeyPairs API)
- `Bucket.php` ✅ - Lightsail 存储桶实体 (来源: GetBucket, GetBuckets API)
- `Database.php` ✅ - Lightsail 数据库实体 (来源: GetRelationalDatabase, GetRelationalDatabases API)
- `DatabaseSnapshot.php` ✅ - 数据库快照实体 (来源: GetRelationalDatabaseSnapshot, GetRelationalDatabaseSnapshots API)
- `LoadBalancer.php` ✅ - 负载均衡器实体 (来源: GetLoadBalancer, GetLoadBalancers API)
- `Alarm.php` ✅ - 告警实体 (来源: GetAlarms API)
- `ContactMethod.php` ✅ - 联系方式实体 (来源: GetContactMethods API)
- `ContainerService.php` ✅ - 容器服务实体 (来源: GetContainerServices API)
- `Operation.php` ✅ - 操作实体 (来源: GetOperation, GetOperations API)

#### Exception 异常处理 🔴

- `LightsailException.php` 🔴 - 基础异常类
- `ConnectionException.php` 🔴 - 连接异常
- `ResourceNotFoundException.php` 🔴 - 资源未找到异常
- `ValidationException.php` 🔴 - 验证异常
- `OperationFailedException.php` 🔴 - 操作失败异常

#### Factory 工厂类 🔴

- `LightsailClientFactory.php` 🔴 - Lightsail 客户端工厂

#### Repository 资源仓库 🔴

- `AwsCredentialRepository.php` 🔴 - AWS 凭证仓库
- `InstanceRepository.php` 🔴 - 实例仓库
- `SnapshotRepository.php` 🔴 - 快照仓库
- `DiskRepository.php` 🔴 - 磁盘仓库
- `DiskSnapshotRepository.php` 🔴 - 磁盘快照仓库
- `StaticIpRepository.php` 🔴 - 静态 IP 仓库
- `DomainRepository.php` 🔴 - 域名仓库
- `DistributionRepository.php` 🔴 - Lightsail CDN 分发仓库
- `CertificateRepository.php` 🔴 - 证书仓库
- `KeyPairRepository.php` 🔴 - 密钥对仓库
- `BucketRepository.php` 🔴 - Lightsail 存储桶仓库
- `DatabaseRepository.php` 🔴 - 数据库仓库
- `DatabaseSnapshotRepository.php` 🔴 - 数据库快照仓库
- `LoadBalancerRepository.php` 🔴 - 负载均衡器仓库
- `AlarmRepository.php` 🔴 - 告警仓库
- `ContactMethodRepository.php` 🔴 - 联系方式仓库
- `ContainerServiceRepository.php` 🔴 - 容器服务仓库
- `MetricRepository.php` 🔴 - 指标仓库
- `OperationRepository.php` 🔴 - 操作仓库

#### Service 服务类 🔴

- `AwsCredentialService.php` 🔴 - AWS 凭证服务
- `InstanceService.php` 🔴 - 实例服务
- `SnapshotService.php` 🔴 - 快照服务
- `DiskService.php` 🔴 - 磁盘服务
- `StaticIpService.php` 🔴 - 静态 IP 服务
- `DomainService.php` 🔴 - 域名服务
- `DistributionService.php` 🔴 - Lightsail CDN 分发服务
- `CertificateService.php` 🔴 - 证书服务
- `KeyPairService.php` 🔴 - 密钥对服务
- `BucketService.php` 🔴 - Lightsail 存储桶服务
- `DatabaseService.php` 🔴 - 数据库服务
- `LoadBalancerService.php` 🔴 - 负载均衡器服务
- `AlarmService.php` 🔴 - 告警服务
- `ContainerService.php` 🔴 - 容器服务
- `MetricService.php` 🔴 - 指标服务

#### 测试 🔴

- **Entity 测试** 🔴 - 实体测试
- **Repository 测试** 🔴 - 仓库测试
- **Service 测试** 🔴 - 服务测试

## 开发任务

### 第一阶段：基础设施

1. **实现 LightsailClientFactory 类** 🔴
   - 创建 AWS Lightsail 客户端实例 🔴
   - 支持多区域配置 🔴
   - 处理认证和凭证 🔴

2. **实现 AWS 凭证管理系统** 🔴
   - 创建 `AwsCredential` 和 `AwsCredentialRepository` 🔴
   - 支持多个 AWS 凭证的存储和管理 🔴
   - 提供凭证切换和选择机制 🔴

3. **创建基础异常类** 🔴
   - `LightsailException` 🔴
   - `ConnectionException` 🔴
   - `ResourceNotFoundException` 🔴
   - `ValidationException` 🔴
   - `OperationFailedException` 🔴

### 第二阶段：资源仓库

#### 1. 实例管理 🔴

- **InstanceRepository** 🔴
  - 创建实例 (CreateInstances API) 🔴
  - 删除实例 (DeleteInstance API) 🔴
  - 启动实例 (StartInstance API) 🔴
  - 停止实例 (StopInstance API) 🔴
  - 重启实例 (RebootInstance API) 🔴
  - 查询实例状态 (GetInstance, GetInstanceState API) 🔴
  - 查询实例端口状态 (GetInstancePortStates API) 🔴

#### 2. 磁盘和存储管理 🔴

- **DiskRepository** 🔴
  - 创建磁盘 (CreateDisk API) 🔴
  - 挂载磁盘 (AttachDisk API) 🔴
  - 分离磁盘 (DetachDisk API) 🔴
  - 删除磁盘 (DeleteDisk API) 🔴
- **DiskSnapshotRepository** 🔴
  - 创建磁盘快照 (CreateDiskSnapshot API) 🔴
  - 删除磁盘快照 (DeleteDiskSnapshot API) 🔴

#### 3. 快照管理 🔴

- **SnapshotRepository** 🔴
  - 创建快照 (CreateInstanceSnapshot API) 🔴
  - 删除快照 (DeleteInstanceSnapshot API) 🔴
  - 从快照创建实例 (CreateInstancesFromSnapshot API) 🔴
  - 列出可用快照 (GetInstanceSnapshots API) 🔴

#### 4. 静态 IP 管理 🔴

- **StaticIpRepository** 🔴
  - 分配静态 IP (AllocateStaticIp API) 🔴
  - 释放静态 IP (ReleaseStaticIp API) 🔴
  - 附加静态 IP (AttachStaticIp API) 🔴
  - 分离静态 IP (DetachStaticIp API) 🔴

#### 5. 域名和 DNS 管理 🔴

- **DomainRepository** 🔴
  - 创建域名 (CreateDomain API) 🔴
  - 删除域名 (DeleteDomain API) 🔴
  - 创建域名记录 (CreateDomainEntry API) 🔴
  - 删除域名记录 (DeleteDomainEntry API) 🔴
  - 更新域名入口 (UpdateDomainEntry API) 🔴

#### 6. Lightsail CDN 分发管理 🔴

- **DistributionRepository** 🔴
  - 创建 CDN 分发 (CreateDistribution API) 🔴
  - 更新 CDN 分发 (UpdateDistribution API) 🔴
  - 删除 CDN 分发 (DeleteDistribution API) 🔴
  - 附加证书到 CDN 分发 (AttachCertificateToDistribution API) 🔴
  - 分离证书 (DetachCertificateFromDistribution API) 🔴
  - 重置缓存 (ResetDistributionCache API) 🔴

#### 7. 密钥对管理 🔴

- **KeyPairRepository** 🔴
  - 创建密钥对 (CreateKeyPair API) 🔴
  - 导入密钥对 (ImportKeyPair API) 🔴
  - 删除密钥对 (DeleteKeyPair API) 🔴
  - 下载默认密钥对 (DownloadDefaultKeyPair API) 🔴

#### 8. Lightsail 存储桶管理 🔴

- **BucketRepository** 🔴
  - 创建存储桶 (CreateBucket API) 🔴
  - 删除存储桶 (DeleteBucket API) 🔴
  - 更新存储桶 (UpdateBucket API) 🔴
  - 管理存储桶访问权限 (SetResourceAccessForBucket API) 🔴

#### 9. Lightsail 数据库管理 🔴

- **DatabaseRepository** 🔴
  - 创建数据库 (CreateRelationalDatabase API) 🔴
  - 更新数据库 (UpdateRelationalDatabase API) 🔴
  - 删除数据库 (DeleteRelationalDatabase API) 🔴
  - 重启数据库 (RebootRelationalDatabase API) 🔴
- **DatabaseSnapshotRepository** 🔴
  - 创建数据库快照 (CreateRelationalDatabaseSnapshot API) 🔴
  - 删除数据库快照 (DeleteRelationalDatabaseSnapshot API) 🔴

#### 10. 负载均衡器管理 🔴

- **LoadBalancerRepository** 🔴
  - 创建负载均衡器 (CreateLoadBalancer API) 🔴
  - 删除负载均衡器 (DeleteLoadBalancer API) 🔴
  - 附加实例到负载均衡器 (AttachInstancesToLoadBalancer API) 🔴
  - 分离实例 (DetachInstancesFromLoadBalancer API) 🔴
  - 更新属性 (UpdateLoadBalancerAttribute API) 🔴

#### 11. 容器服务管理 🔴

- **ContainerServiceRepository** 🔴
  - 创建容器服务 (CreateContainerService API) 🔴
  - 部署容器服务 (CreateContainerServiceDeployment API) 🔴
  - 删除容器服务 (DeleteContainerService API) 🔴
  - 管理容器镜像 (RegisterContainerImage API) 🔴

#### 12. 监控和告警 🔴

- **MetricRepository** 🔴
  - 获取实例指标数据 (GetInstanceMetricData API) 🔴
  - 获取存储桶指标数据 (GetBucketMetricData API) 🔴
  - 获取负载均衡器指标数据 (GetLoadBalancerMetricData API) 🔴
  - 获取分发指标数据 (GetDistributionMetricData API) 🔴
- **AlarmRepository** 🔴
  - 创建告警 (PutAlarm API) 🔴
  - 删除告警 (DeleteAlarm API) 🔴
  - 测试告警 (TestAlarm API) 🔴

#### 13. 联系方式管理 🔴

- **ContactMethodRepository** 🔴
  - 添加联系方式 (CreateContactMethod API) 🔴
  - 验证联系方式 (SendContactMethodVerification API) 🔴
  - 删除联系方式 (DeleteContactMethod API) 🔴

### 第三阶段：服务层

#### 1. AWS 凭证服务 🔴

- **AwsCredentialService** 🔴
  - 凭证管理高级接口 🔴
  - 多账户管理功能 🔴
  - 凭证切换和访问机制 🔴

#### 2. 实例服务 🔴

- **InstanceService** 🔴
  - 基本 CRUD 功能封装 🔴
  - 批量操作功能 🔴
  - 自动扩展功能 🔴

#### 3. 存储服务 🔴

- **DiskService** 🔴
  - 磁盘管理高级功能 🔴
- **BucketService** 🔴
  - 存储桶管理高级功能 🔴
  - 文件上传下载 🔴
  - 存储策略配置 🔴

#### 4. 网络服务 🔴

- **StaticIpService** 🔴
  - 静态 IP 管理高级功能 🔴
- **LoadBalancerService** 🔴
  - 负载均衡器管理高级功能 🔴
  - 网络配置和优化 🔴

#### 5. 域名和 CDN 服务 🔴

- **DomainService** 🔴
  - 域名管理高级功能 🔴
- **DistributionService** 🔴
  - CDN 配置和缓存管理 🔴
- **CertificateService** 🔴
  - 证书管理服务 🔴
  - 自动化配置和优化 🔴

#### 6. 快照和备份服务 🔴

- **SnapshotService** 🔴
  - 备份管理高级功能 🔴
  - 定时快照功能 🔴
  - 轮转策略 🔴
  - 自动备份恢复 🔴

#### 7. 数据库服务 🔴

- **DatabaseService** 🔴
  - 数据库管理高级功能 🔴
  - 备份恢复功能 🔴
  - 参数配置功能 🔴
  - 监控和优化 🔴

#### 8. 容器服务 🔴

- **ContainerService** 🔴
  - 容器服务管理高级功能 🔴
  - 容器部署和扩展 🔴
  - 容器健康检查和监控 🔴

#### 9. 监控和告警服务 🔴

- **MetricService** 🔴
  - 指标数据分析 🔴
  - 数据可视化功能 🔴
- **AlarmService** 🔴
  - 告警管理和通知 🔴
  - 自定义监控和报表 🔴

### 第四阶段：命令行工具

#### 1. 实例管理命令 🔴

- `lightsail:instance:list` 🔴 - 列出所有实例
- `lightsail:instance:create` 🔴 - 创建新实例
- `lightsail:instance:start` 🔴 - 启动实例
- `lightsail:instance:stop` 🔴 - 停止实例
- `lightsail:instance:restart` 🔴 - 重启实例

#### 2. 磁盘管理命令 🔴

- `lightsail:disk:list` 🔴 - 列出所有磁盘
- `lightsail:disk:create` 🔴 - 创建新磁盘
- `lightsail:disk:attach` 🔴 - 挂载磁盘
- `lightsail:disk:detach` 🔴 - 分离磁盘

#### 3. 快照管理命令 🔴

- `lightsail:snapshot:create` 🔴 - 创建快照
- `lightsail:snapshot:list` 🔴 - 列出快照
- `lightsail:snapshot:restore` 🔴 - 从快照恢复

#### 4. 静态 IP 管理命令 🔴

- `lightsail:static-ip:allocate` 🔴 - 分配静态 IP
- `lightsail:static-ip:attach` 🔴 - 附加静态 IP 到实例
- `lightsail:static-ip:detach` 🔴 - 分离静态 IP
- `lightsail:static-ip:release` 🔴 - 释放静态 IP

#### 5. 域名管理命令 🔴

- `lightsail:domain:list` 🔴 - 列出域名
- `lightsail:domain:create` 🔴 - 创建域名
- `lightsail:domain:delete` 🔴 - 删除域名
- `lightsail:domain:create-entry` 🔴 - 创建域名记录

#### 6. CDN 分发管理命令 🔴

- `lightsail:distribution:list` 🔴 - 列出 Lightsail CDN 分发
- `lightsail:distribution:create` 🔴 - 创建 CDN 分发
- `lightsail:distribution:update` 🔴 - 更新 CDN 分发
- `lightsail:distribution:delete` 🔴 - 删除 CDN 分发

#### 7. 存储桶管理命令 🔴

- `lightsail:bucket:list` 🔴 - 列出存储桶
- `lightsail:bucket:create` 🔴 - 创建存储桶
- `lightsail:bucket:delete` 🔴 - 删除存储桶

#### 8. 数据库管理命令 🔴

- `lightsail:database:list` 🔴 - 列出数据库
- `lightsail:database:create` 🔴 - 创建数据库
- `lightsail:database:delete` 🔴 - 删除数据库
- `lightsail:database:restart` 🔴 - 重启数据库

#### 9. 负载均衡器管理命令 🔴

- `lightsail:load-balancer:list` 🔴 - 列出负载均衡器
- `lightsail:load-balancer:create` 🔴 - 创建负载均衡器
- `lightsail:load-balancer:delete` 🔴 - 删除负载均衡器
- `lightsail:load-balancer:attach-instances` 🔴 - 附加实例到负载均衡器

#### 10. 容器服务管理命令 🔴

- `lightsail:container:list` 🔴 - 列出容器服务
- `lightsail:container:create` 🔴 - 创建容器服务
- `lightsail:container:deploy` 🔴 - 部署容器服务
- `lightsail:container:delete` 🔴 - 删除容器服务

#### 11. 监控命令 🔴

- `lightsail:metric:get` 🔴 - 获取资源指标
- `lightsail:alarm:list` 🔴 - 列出告警
- `lightsail:alarm:create` 🔴 - 创建告警
- `lightsail:alarm:delete` 🔴 - 删除告警

## 测试计划

### 单元测试 🔴

- Entity 单元测试 🔴
- Repository 单元测试 🔴
- Service 单元测试 🔴
- Command 单元测试 🔴
- 模拟 AWS SDK 响应测试 🔴

### 集成测试 🔴

- AWS Lightsail API 集成测试 🔴
- 命令行工具集成测试 🔴

### 性能测试 🔴

- 资源批量处理性能测试 🔴
- 并发请求处理测试 🔴

## 文档计划

### API 文档 🔴

- Entity PHPDoc 文档 🔴
- Repository PHPDoc 文档 🔴
- Service PHPDoc 文档 🔴
- Command PHPDoc 文档 🔴
- 生成 API 参考文档 🔴

### 使用指南 🔴

- README.md 更新 🔴
- README.zh-CN.md 更新 🔴
- 安装和配置指南 🔴
- 常见用例示例 🔴

### 示例代码 🔴

- 各场景示例代码 🔴
- Symfony Bundle 配置示例 🔴

## 发布计划

### 内部测试版本 🔴

- 基础功能开发 🔴
- 内部测试 🔴
- 代码审查 🔴

### Beta 版本 🔴

- 基本功能 beta 版本发布 🔴
- 用户反馈收集 🔴

### 稳定版本 🔴

- 1.0.0 稳定版发布 🔴
- 完整文档提供 🔴
- 示例代码提供 🔴

## 相关资源 📚

- [AWS Lightsail API 参考](https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/Welcome.html)
- [AWS SDK for PHP - Lightsail](https://docs.aws.amazon.com/aws-sdk-php/latest/class-Aws.Lightsail.LightsailClient.html)
- [Symfony 文档](https://symfony.com/doc/current/index.html)
