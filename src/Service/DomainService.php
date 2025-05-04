<?php

namespace AwsLightsailBundle\Service;

use AwsLightsailBundle\Client\LightsailApiClient;
use AwsLightsailBundle\Request\CreateDomainEntryRequest;
use AwsLightsailBundle\Request\CreateDomainRequest;
use AwsLightsailBundle\Request\DeleteDomainEntryRequest;
use AwsLightsailBundle\Request\DeleteDomainRequest;
use AwsLightsailBundle\Request\GetDomainRequest;
use AwsLightsailBundle\Request\GetDomainsRequest;
use Psr\Log\LoggerInterface;

/**
 * 域名服务类
 * 
 * 该类负责AWS Lightsail域名资源相关操作
 */
class DomainService
{
    public function __construct(
        private readonly LightsailApiClient $lightsailApiClient,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 创建域名
     */
    public function createDomain(
        string $domainName,
        array $tags,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $this->logger->info('创建域名', [
            'domainName' => $domainName,
            'region' => $region
        ]);

        $request = new CreateDomainRequest($domainName, $tags);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $this->logger->info('成功创建域名', [
                'response' => $response
            ]);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('创建域名失败', [
                'exception' => $e,
                'domainName' => $domainName
            ]);
            throw $e;
        }
    }

    /**
     * 获取域名列表
     */
    public function getDomains(
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetDomainsRequest();
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取域名列表失败', [
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * 获取域名详情
     */
    public function getDomain(
        string $domainName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new GetDomainRequest($domainName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('获取域名详情失败', [
                'exception' => $e,
                'domainName' => $domainName
            ]);
            throw $e;
        }
    }

    /**
     * 创建域名记录
     */
    public function createDomainEntry(
        string $domainName,
        string $name,
        string $type,
        string $target,
        int $ttl,
        bool $isAlias,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $this->logger->info('创建域名记录', [
            'domainName' => $domainName,
            'name' => $name,
            'type' => $type,
            'target' => $target,
            'region' => $region
        ]);

        $request = new CreateDomainEntryRequest(
            $domainName,
            $name,
            $type,
            $target,
            $ttl,
            $isAlias
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            $response = $this->lightsailApiClient->request($request);
            $this->logger->info('成功创建域名记录', [
                'response' => $response
            ]);
            return $response;
        } catch (\Exception $e) {
            $this->logger->error('创建域名记录失败', [
                'exception' => $e,
                'domainName' => $domainName,
                'name' => $name
            ]);
            throw $e;
        }
    }

    /**
     * 删除域名记录
     */
    public function deleteDomainEntry(
        string $domainName,
        string $name,
        string $type,
        string $value,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new DeleteDomainEntryRequest(
            $domainName,
            $name,
            $type,
            $value
        );
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('删除域名记录失败', [
                'exception' => $e,
                'domainName' => $domainName,
                'name' => $name
            ]);
            throw $e;
        }
    }

    /**
     * 删除域名
     */
    public function deleteDomain(
        string $domainName,
        string $accessKey,
        string $secretKey,
        string $region
    ): array {
        $request = new DeleteDomainRequest($domainName);
        $request->setCredentials($accessKey, $secretKey, $region);

        try {
            return $this->lightsailApiClient->request($request);
        } catch (\Exception $e) {
            $this->logger->error('删除域名失败', [
                'exception' => $e,
                'domainName' => $domainName
            ]);
            throw $e;
        }
    }
} 