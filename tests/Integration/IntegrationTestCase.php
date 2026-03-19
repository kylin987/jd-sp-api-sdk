<?php

declare(strict_types=1);

namespace JdSpApiSdk\Tests\Integration;

use com\jd\openapi\apiClient\Configuration;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    protected function createConfiguration(): Configuration
    {
        $appKey = getenv('JD_APP_KEY') ?: '';
        $appSecret = getenv('JD_APP_SECRET') ?: '';
        $accessToken = getenv('JD_ACCESS_TOKEN') ?: '';

        if ($appKey === '' || $appSecret === '' || $accessToken === '') {
            self::markTestSkipped(
                'Live API credentials are missing. Set JD_APP_KEY, JD_APP_SECRET, and JD_ACCESS_TOKEN.'
            );
        }

        return Configuration::getDefaultConfiguration()
            ->setAppKey($appKey)
            ->setAppSecret($appSecret)
            ->setAccessToken($accessToken);
    }
}
