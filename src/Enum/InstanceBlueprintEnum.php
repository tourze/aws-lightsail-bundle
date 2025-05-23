<?php

namespace AwsLightsailBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 实例蓝图枚举
 *
 * @see https://docs.aws.amazon.com/lightsail/2016-11-28/api-reference/API_Blueprint.html AWS Lightsail 蓝图 API 文档
 */
enum InstanceBlueprintEnum: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    // Linux/Unix 蓝图
    case AMAZON_LINUX_2 = 'amazon_linux_2';
    case AMAZON_LINUX_2023 = 'amazon_linux_2023';
    case UBUNTU_18_04 = 'ubuntu_18_04';
    case UBUNTU_20_04 = 'ubuntu_20_04';
    case UBUNTU_22_04 = 'ubuntu_22_04';
    case DEBIAN_10 = 'debian_10';
    case DEBIAN_11 = 'debian_11';
    case DEBIAN_12 = 'debian_12';

    // 应用蓝图
    case LAMP_UBUNTU_20_04 = 'lamp_ubuntu_20_04';
    case NGINX_UBUNTU_20_04 = 'nginx_ubuntu_20_04';
    case WORDPRESS_UBUNTU_20_04 = 'wordpress_ubuntu_20_04';
    case MEAN_UBUNTU_20_04 = 'mean_ubuntu_20_04';
    case NODE_JS_UBUNTU_20_04 = 'nodejs_ubuntu_20_04';

    // Windows 蓝图
    case WINDOWS_SERVER_2019 = 'windows_server_2019';
    case WINDOWS_SERVER_2022 = 'windows_server_2022';

    public function getLabel(): string
    {
        return match ($this) {
            self::AMAZON_LINUX_2 => 'Amazon Linux 2',
            self::AMAZON_LINUX_2023 => 'Amazon Linux 2023',
            self::UBUNTU_18_04 => 'Ubuntu 18.04 LTS',
            self::UBUNTU_20_04 => 'Ubuntu 20.04 LTS',
            self::UBUNTU_22_04 => 'Ubuntu 22.04 LTS',
            self::DEBIAN_10 => 'Debian 10',
            self::DEBIAN_11 => 'Debian 11',
            self::DEBIAN_12 => 'Debian 12',
            self::LAMP_UBUNTU_20_04 => 'LAMP (Ubuntu 20.04)',
            self::NGINX_UBUNTU_20_04 => 'NGINX (Ubuntu 20.04)',
            self::WORDPRESS_UBUNTU_20_04 => 'WordPress (Ubuntu 20.04)',
            self::MEAN_UBUNTU_20_04 => 'MEAN (Ubuntu 20.04)',
            self::NODE_JS_UBUNTU_20_04 => 'Node.js (Ubuntu 20.04)',
            self::WINDOWS_SERVER_2019 => 'Windows Server 2019',
            self::WINDOWS_SERVER_2022 => 'Windows Server 2022',
        };
    }

    /**
     * 从字符串ID转换为对应的枚举值
     */
    public static function fromString(string $blueprintId): self
    {
        return match ($blueprintId) {
            'amazon_linux_2' => self::AMAZON_LINUX_2,
            'amazon_linux_2023' => self::AMAZON_LINUX_2023,
            'ubuntu_18_04' => self::UBUNTU_18_04,
            'ubuntu_20_04' => self::UBUNTU_20_04,
            'ubuntu_22_04' => self::UBUNTU_22_04,
            'debian_10' => self::DEBIAN_10,
            'debian_11' => self::DEBIAN_11,
            'debian_12' => self::DEBIAN_12,
            'lamp_ubuntu_20_04' => self::LAMP_UBUNTU_20_04,
            'nginx_ubuntu_20_04' => self::NGINX_UBUNTU_20_04,
            'wordpress_ubuntu_20_04' => self::WORDPRESS_UBUNTU_20_04,
            'mean_ubuntu_20_04' => self::MEAN_UBUNTU_20_04,
            'nodejs_ubuntu_20_04' => self::NODE_JS_UBUNTU_20_04,
            'windows_server_2019' => self::WINDOWS_SERVER_2019,
            'windows_server_2022' => self::WINDOWS_SERVER_2022,
            default => self::UBUNTU_20_04, // 默认值
        };
    }
}
