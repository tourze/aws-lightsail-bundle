<?php

declare(strict_types=1);

namespace ChubbyphpTest\AwsLightsailBundle\Unit\Enum;

use AwsLightsailBundle\Enum\DnsRecordTypeEnum;
use PHPUnit\Framework\TestCase;

final class DnsRecordTypeEnumTest extends TestCase
{
    public function testCases(): void
    {
        $expectedCases = [
            'A',
            'AAAA',
            'CNAME',
            'MX',
            'NS',
            'SOA',
            'SRV',
            'TXT',
            'CAA',
        ];

        $actualCases = array_map(fn($case) => $case->name, DnsRecordTypeEnum::cases());

        $this->assertSame($expectedCases, $actualCases);
    }

    public function testValues(): void
    {
        $expectedValues = [
            'A',
            'AAAA',
            'CNAME',
            'MX',
            'NS',
            'SOA',
            'SRV',
            'TXT',
            'CAA',
        ];

        $actualValues = array_map(fn($case) => $case->value, DnsRecordTypeEnum::cases());

        $this->assertSame($expectedValues, $actualValues);
    }

    public function testLabels(): void
    {
        $this->assertSame('A 记录 (IPv4)', DnsRecordTypeEnum::A->getLabel());
        $this->assertSame('AAAA 记录 (IPv6)', DnsRecordTypeEnum::AAAA->getLabel());
        $this->assertSame('CNAME 记录', DnsRecordTypeEnum::CNAME->getLabel());
        $this->assertSame('MX 记录 (邮件)', DnsRecordTypeEnum::MX->getLabel());
        $this->assertSame('NS 记录 (域名服务器)', DnsRecordTypeEnum::NS->getLabel());
        $this->assertSame('SOA 记录 (授权起始)', DnsRecordTypeEnum::SOA->getLabel());
        $this->assertSame('SRV 记录 (服务)', DnsRecordTypeEnum::SRV->getLabel());
        $this->assertSame('TXT 记录 (文本)', DnsRecordTypeEnum::TXT->getLabel());
        $this->assertSame('CAA 记录 (证书授权)', DnsRecordTypeEnum::CAA->getLabel());
    }

    public function testEnumValues(): void
    {
        $this->assertSame('A', DnsRecordTypeEnum::A->value);
        $this->assertSame('AAAA', DnsRecordTypeEnum::AAAA->value);
        $this->assertSame('CNAME', DnsRecordTypeEnum::CNAME->value);
        $this->assertSame('MX', DnsRecordTypeEnum::MX->value);
        $this->assertSame('NS', DnsRecordTypeEnum::NS->value);
        $this->assertSame('SOA', DnsRecordTypeEnum::SOA->value);
        $this->assertSame('SRV', DnsRecordTypeEnum::SRV->value);
        $this->assertSame('TXT', DnsRecordTypeEnum::TXT->value);
        $this->assertSame('CAA', DnsRecordTypeEnum::CAA->value);
    }

    public function testCasesAreUnique(): void
    {
        $cases = DnsRecordTypeEnum::cases();
        $names = array_map(fn($case) => $case->name, $cases);
        $uniqueNames = array_unique($names);

        $this->assertCount(count($names), $uniqueNames, '所有枚举名称必须唯一');
    }

    public function testValuesAreUnique(): void
    {
        $cases = DnsRecordTypeEnum::cases();
        $values = array_map(fn($case) => $case->value, $cases);
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, '所有枚举值必须唯一');
    }

    public function testLabelsAreUnique(): void
    {
        $cases = DnsRecordTypeEnum::cases();
        $labels = array_map(fn($case) => $case->getLabel(), $cases);
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, '所有标签必须唯一');
    }

    public function testTotalRecordTypeCount(): void
    {
        $this->assertCount(9, DnsRecordTypeEnum::cases(), '总共应该有9个DNS记录类型');
    }

    public function testIpAddressRecords(): void
    {
        $ipRecords = [
            DnsRecordTypeEnum::A,
            DnsRecordTypeEnum::AAAA,
        ];

        foreach ($ipRecords as $record) {
            $this->assertStringContainsString('IPv', $record->getLabel());
        }
    }

    public function testNameResolutionRecords(): void
    {
        $nameRecords = [
            DnsRecordTypeEnum::CNAME,
            DnsRecordTypeEnum::NS,
        ];

        foreach ($nameRecords as $record) {
            $this->assertStringNotContainsString('IPv', $record->getLabel());
        }
    }

    public function testServiceRecords(): void
    {
        $serviceRecords = [
            DnsRecordTypeEnum::MX,
            DnsRecordTypeEnum::SRV,
        ];

        foreach ($serviceRecords as $record) {
            $label = $record->getLabel();
            $this->assertTrue(
                str_contains($label, '邮件') || str_contains($label, '服务'),
                "Service record label should contain service indicator: {$label}"
            );
        }
    }

    public function testMetadataRecords(): void
    {
        $metadataRecords = [
            DnsRecordTypeEnum::SOA,
            DnsRecordTypeEnum::TXT,
            DnsRecordTypeEnum::CAA,
        ];

        foreach ($metadataRecords as $record) {
            $this->assertStringNotContainsString('IPv', $record->getLabel());
        }
    }

    public function testAllValuesAreUppercase(): void
    {
        foreach (DnsRecordTypeEnum::cases() as $enum) {
            $value = $enum->value;
            $this->assertSame($value, strtoupper($value), "DNS record type value should be uppercase: {$value}");
        }
    }

    public function testAllLabelsContainRecordText(): void
    {
        foreach (DnsRecordTypeEnum::cases() as $enum) {
            $label = $enum->getLabel();
            $this->assertStringContainsString('记录', $label, "DNS record label should contain '记录': {$label}");
        }
    }

    public function testIPv4Record(): void
    {
        $this->assertSame('A', DnsRecordTypeEnum::A->value);
        $this->assertStringContainsString('IPv4', DnsRecordTypeEnum::A->getLabel());
    }

    public function testIPv6Record(): void
    {
        $this->assertSame('AAAA', DnsRecordTypeEnum::AAAA->value);
        $this->assertStringContainsString('IPv6', DnsRecordTypeEnum::AAAA->getLabel());
    }

    public function testCertificateAuthorityRecord(): void
    {
        $this->assertSame('CAA', DnsRecordTypeEnum::CAA->value);
        $this->assertStringContainsString('证书', DnsRecordTypeEnum::CAA->getLabel());
    }

    public function testMailExchangeRecord(): void
    {
        $this->assertSame('MX', DnsRecordTypeEnum::MX->value);
        $this->assertStringContainsString('邮件', DnsRecordTypeEnum::MX->getLabel());
    }

    public function testStartOfAuthorityRecord(): void
    {
        $this->assertSame('SOA', DnsRecordTypeEnum::SOA->value);
        $this->assertStringContainsString('授权', DnsRecordTypeEnum::SOA->getLabel());
    }

    public function testAllEnumValues_haveLabels(): void
    {
        foreach (DnsRecordTypeEnum::cases() as $enum) {
            $label = $enum->getLabel();
            $this->assertIsString($label);
            $this->assertNotEmpty($label);
        }
    }
} 