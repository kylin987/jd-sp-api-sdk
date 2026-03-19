<?php

declare(strict_types=1);

namespace JdSpApiSdk\Tests\Unit;

use com\jd\openapi\apiClient\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    public function testDefaultConfigurationExposesExpectedDefaults(): void
    {
        $config = Configuration::getDefaultConfiguration();

        $this->assertSame('https://api-cn.jd.com/rest', $config->getHost());
        $this->assertSame('OpenAPI-Generator/1.0.0/PHP', $config->getUserAgent());
        $this->assertSame(sys_get_temp_dir(), $config->getTempFolderPath());
    }

    public function testCredentialMutatorsPersistValues(): void
    {
        $config = new Configuration();

        $config
            ->setAppKey('app-key')
            ->setAppSecret('app-secret')
            ->setAccessToken('access-token')
            ->setRequestIdentity('vender');

        $this->assertSame('app-key', $config->getAppKey());
        $this->assertSame('app-secret', $config->getAppSecret());
        $this->assertSame('access-token', $config->getAccessToken());
        $this->assertSame('vender', $config->getRequestIdentity());
    }
}
