# AWS Lightsail Bundle 测试计划

## 测试概述

本文档记录 AWS Lightsail Bundle 的测试用例创建和执行情况。

## 测试目标

- 📋 实现 100% 测试覆盖率
- 🎯 覆盖所有核心功能和边界情况
- 🚀 确保测试用例快速执行
- 🔒 保证测试用例独立性和可重复性

## 测试类别

### 1. Entity 实体类测试 📦

| 文件 | 测试类 | 核心场景 | 状态 |
|------|--------|----------|------|
| `Entity/AwsCredential.php` | `AwsCredentialTest` | 构造、getter/setter、toString | ✅ 已完成 |
| `Entity/Instance.php` | `InstanceTest` | 实例状态管理、网络配置、硬件配置 | ✅ 已完成 |
| `Entity/Snapshot.php` | `SnapshotTest` | 快照类型、状态管理、关联关系 | ⏳ 待开始 |
| `Entity/Disk.php` | `DiskTest` | 磁盘状态、挂载状态、容量管理 | ⏳ 待开始 |
| `Entity/DiskSnapshot.php` | `DiskSnapshotTest` | 磁盘快照状态、恢复配置 | ⏳ 待开始 |
| `Entity/StaticIp.php` | `StaticIpTest` | IP分配、实例绑定状态 | ⏳ 待开始 |
| `Entity/Domain.php` | `DomainTest` | 域名管理、DNS记录关联 | ⏳ 待开始 |
| `Entity/DomainEntry.php` | `DomainEntryTest` | DNS记录类型、别名配置 | ⏳ 待开始 |
| `Entity/Distribution.php` | `DistributionTest` | CDN配置、缓存行为、证书绑定 | ⏳ 待开始 |
| `Entity/Certificate.php` | `CertificateTest` | 证书状态、域名验证、有效期管理 | ⏳ 待开始 |
| `Entity/KeyPair.php` | `KeyPairTest` | 密钥对管理、加密状态 | ✅ 已完成 |
| `Entity/Bucket.php` | `BucketTest` | 存储桶配置、访问规则、CORS | ⏳ 待开始 |
| `Entity/Database.php` | `DatabaseTest` | 数据库配置、备份设置、连接参数 | ⏳ 待开始 |
| `Entity/DatabaseSnapshot.php` | `DatabaseSnapshotTest` | 数据库快照、恢复点管理 | ⏳ 待开始 |
| `Entity/LoadBalancer.php` | `LoadBalancerTest` | 负载均衡配置、健康检查、实例管理 | ⏳ 待开始 |
| `Entity/Alarm.php` | `AlarmTest` | 告警配置、指标监控、通知设置 | ⏳ 待开始 |
| `Entity/ContactMethod.php` | `ContactMethodTest` | 联系方式验证、通知协议 | ⏳ 待开始 |
| `Entity/ContainerService.php` | `ContainerServiceTest` | 容器服务配置、扩展设置 | ⏳ 待开始 |
| `Entity/Operation.php` | `OperationTest` | 操作状态、错误处理、元数据 | ⏳ 待开始 |

### 2. Enum 枚举类测试 🎭

| 文件 | 测试类 | 核心场景 | 状态 |
|------|--------|----------|------|
| `Enum/AlarmMetricEnum.php` | `AlarmMetricEnumTest` | 枚举值、标签显示、选择器功能 | ⏳ 待开始 |
| `Enum/AlarmStateEnum.php` | `AlarmStateEnumTest` | 状态值、中文标签 | ⏳ 待开始 |
| `Enum/AmazonRegion.php` | `AmazonRegionTest` | 区域代码、区域名称映射 | ⏳ 待开始 |
| `Enum/BucketAccessRuleEnum.php` | `BucketAccessRuleEnumTest` | 访问规则类型 | ⏳ 待开始 |
| `Enum/CertificateStatusEnum.php` | `CertificateStatusEnumTest` | 证书状态类型 | ⏳ 待开始 |
| `Enum/ContactMethodStatusEnum.php` | `ContactMethodStatusEnumTest` | 联系方式状态 | ⏳ 待开始 |
| `Enum/ContactMethodTypeEnum.php` | `ContactMethodTypeEnumTest` | 联系方式类型 | ⏳ 待开始 |
| `Enum/ContainerServicePowerEnum.php` | `ContainerServicePowerEnumTest` | 容器服务规格 | ⏳ 待开始 |
| `Enum/ContainerServiceStateEnum.php` | `ContainerServiceStateEnumTest` | 容器服务状态 | ⏳ 待开始 |
| `Enum/DatabaseEngineEnum.php` | `DatabaseEngineEnumTest` | 数据库引擎类型 | ⏳ 待开始 |
| `Enum/DatabaseStatusEnum.php` | `DatabaseStatusEnumTest` | 数据库状态 | ⏳ 待开始 |
| `Enum/DiskStateEnum.php` | `DiskStateEnumTest` | 磁盘状态 | ✅ 已完成 |
| `Enum/DistributionStatusEnum.php` | `DistributionStatusEnumTest` | 分发状态 | ⏳ 待开始 |
| `Enum/DnsRecordTypeEnum.php` | `DnsRecordTypeEnumTest` | DNS记录类型 | ✅ 已完成 |
| `Enum/InstanceBlueprintEnum.php` | `InstanceBlueprintEnumTest` | 实例蓝图、fromString转换 | ✅ 已完成 |
| `Enum/InstanceBundleEnum.php` | `InstanceBundleEnumTest` | 实例套餐、fromString转换 | ✅ 已完成 |
| `Enum/InstanceStateEnum.php` | `InstanceStateEnumTest` | 实例状态、fromString转换 | ✅ 已完成 |
| `Enum/LoadBalancerStatusEnum.php` | `LoadBalancerStatusEnumTest` | 负载均衡器状态 | ⏳ 待开始 |
| `Enum/OperationStatusEnum.php` | `OperationStatusEnumTest` | 操作状态 | ✅ 已完成 |
| `Enum/OperationTypeEnum.php` | `OperationTypeEnumTest` | 操作类型 | ⏳ 待开始 |
| `Enum/SnapshotTypeEnum.php` | `SnapshotTypeEnumTest` | 快照类型 | ⏳ 待开始 |

### 3. Repository 仓库类测试 🗄️

| 文件 | 测试类 | 核心场景 | 状态 |
|------|--------|----------|------|
| `Repository/AwsCredentialRepository.php` | `AwsCredentialRepositoryTest` | 查找默认凭证、数据库操作 | ⏳ 待开始 |
| `Repository/InstanceRepository.php` | `InstanceRepositoryTest` | 按状态查询、按区域查询、按蓝图查询 | ⏳ 待开始 |
| `Repository/SnapshotRepository.php` | `SnapshotRepositoryTest` | 按类型查询、按日期范围查询 | ⏳ 待开始 |
| `Repository/DiskRepository.php` | `DiskRepositoryTest` | 按状态查询、按挂载实例查询 | ⏳ 待开始 |
| `Repository/DiskSnapshotRepository.php` | `DiskSnapshotRepositoryTest` | 按磁盘查询、自动快照查询 | ⏳ 待开始 |
| `Repository/StaticIpRepository.php` | `StaticIpRepositoryTest` | 按区域查询、已绑定/未绑定查询 | ⏳ 待开始 |
| `Repository/DomainRepository.php` | `DomainRepositoryTest` | 按区域查询、托管域名查询 | ⏳ 待开始 |
| `Repository/DomainEntryRepository.php` | `DomainEntryRepositoryTest` | 按域名查询、按记录类型查询 | ⏳ 待开始 |
| `Repository/DistributionRepository.php` | `DistributionRepositoryTest` | 按状态查询、按证书查询 | ⏳ 待开始 |
| `Repository/CertificateRepository.php` | `CertificateRepositoryTest` | 按状态查询、即将过期证书查询 | ⏳ 待开始 |
| `Repository/KeyPairRepository.php` | `KeyPairRepositoryTest` | 按区域查询、按指纹查询 | ⏳ 待开始 |
| `Repository/BucketRepository.php` | `BucketRepositoryTest` | 按访问规则查询、按大小查询 | ⏳ 待开始 |
| `Repository/DatabaseRepository.php` | `DatabaseRepositoryTest` | 按引擎查询、公共访问查询 | ⏳ 待开始 |
| `Repository/DatabaseSnapshotRepository.php` | `DatabaseSnapshotRepositoryTest` | 按数据库查询、自动快照查询 | ⏳ 待开始 |
| `Repository/LoadBalancerRepository.php` | `LoadBalancerRepositoryTest` | 按状态查询、按实例查询 | ⏳ 待开始 |
| `Repository/AlarmRepository.php` | `AlarmRepositoryTest` | 按资源查询、按状态查询 | ⏳ 待开始 |
| `Repository/ContactMethodRepository.php` | `ContactMethodRepositoryTest` | 按类型查询、按状态查询 | ⏳ 待开始 |
| `Repository/ContainerServiceRepository.php` | `ContainerServiceRepositoryTest` | 按状态查询、按配置查询 | ⏳ 待开始 |
| `Repository/OperationRepository.php` | `OperationRepositoryTest` | 按状态查询、按类型查询、最近操作查询 | ⏳ 待开始 |

### 4. Service 服务类测试 🔧

| 文件 | 测试类 | 核心场景 | 状态 |
|------|--------|----------|------|
| `Service/AdminMenu.php` | `AdminMenuTest` | 菜单构建、链接生成 | ⏳ 待开始 |
| `Service/InstanceSyncService.php` | `InstanceSyncServiceTest` | 实例同步、批量同步、清理删除实例 | ⏳ 待开始 |
| `Service/KeyPairSyncService.php` | `KeyPairSyncServiceTest` | 密钥对同步、批量同步、清理删除密钥对 | ⏳ 待开始 |

### 5. Command 命令类测试 ⌨️

| 文件 | 测试类 | 核心场景 | 状态 |
|------|--------|----------|------|
| `Command/InstanceControlCommand.php` | `InstanceControlCommandTest` | 实例控制命令、交互式选择 | ⏳ 待开始 |
| `Command/InstanceCreateCommand.php` | `InstanceCreateCommandTest` | 实例创建命令、参数验证 | ⏳ 待开始 |
| `Command/InstanceSyncCommand.php` | `InstanceSyncCommandTest` | 实例同步命令、批量同步 | ⏳ 待开始 |

### 6. Controller CRUD 控制器测试 🎮

| 文件 | 测试类 | 核心场景 | 状态 |
|------|--------|----------|------|
| `Controller/Admin/*CrudController.php` | 各CRUD控制器测试类 | CRUD配置、字段配置、动作配置 | ⏳ 待开始 |

### 7. DependencyInjection 依赖注入测试 🔗

| 文件 | 测试类 | 核心场景 | 状态 |
|------|--------|----------|------|
| `DependencyInjection/AwsLightsailExtension.php` | `AwsLightsailExtensionTest` | 服务加载、配置处理 | ⏳ 待开始 |
| `AwsLightsailBundle.php` | `AwsLightsailBundleTest` | Bundle基本功能 | ⏳ 待开始 |

## 测试执行计划

1. ✅ **阶段一：环境检查** - 完成
2. 🔄 **阶段二：Entity测试** - 进行中 (3/19 完成)
3. 🔄 **阶段三：Enum测试** - 进行中 (6/21 完成)
4. ⏳ **阶段四：Repository测试** - 待开始
5. ⏳ **阶段五：Service测试** - 待开始
6. ⏳ **阶段六：Command测试** - 待开始
7. ⏳ **阶段七：Controller测试** - 待开始
8. ⏳ **阶段八：DI测试** - 待开始

## 测试统计

- 📊 **总测试类数**: 75+
- ✅ **已完成**: 9 (Entity: 3, Enum: 6)
- 🔄 **进行中**: 0  
- ⏳ **待开始**: 66+
- 📈 **完成度**: 12.0%
- 🧪 **测试方法数**: 121
- 🎯 **断言数**: 434

## 测试执行命令

```bash
# 在项目根目录执行
./vendor/bin/phpunit packages/aws-lightsail-bundle/tests
```

## 注意事项

- 🚫 不允许修改 src 目录下的代码
- 🚫 不允许引入额外的测试依赖
- 🚫 不允许使用 Symfony PropertyAccessor
- 🚫 不允许使用 symfony/string 包
- ✅ 必须保证测试用例独立性
- ✅ 必须覆盖正常和异常情况
- ✅ 必须保证测试执行速度快 