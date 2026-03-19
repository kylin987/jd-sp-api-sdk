<?php

declare(strict_types=1);

namespace JdSpApiSdk\Tests\Unit;

use com\jd\openapi\apiClient\SignatureCalculator;
use PHPUnit\Framework\TestCase;

final class SignatureCalculatorTest extends TestCase
{
    public function testParameterConvertBuildsSortedSignatureMap(): void
    {
        $actual = SignatureCalculator::parameterConvert(
            [
                'appKey' => 'app-key',
                'accessToken' => 'access-token',
                'timestamp' => '1711111111000',
                'signMethod' => 'md5',
            ],
            ['page' => 1],
            '{"hello":"world"}',
            ['product_id' => 1001]
        );

        $expected = [
            'X-JOS-Access-Token' => 'access-token',
            'X-JOS-App-Key' => 'app-key',
            'X-JOS-Sign-Method' => 'md5',
            'X-JOS-Timestamp' => '1711111111000',
            'body' => '{"hello":"world"}',
            'page' => 1,
            'product_id' => 1001,
        ];

        ksort($expected);

        $this->assertSame($expected, $actual);
    }

    public function testCalculateSignatureReturnsUppercaseMd5Digest(): void
    {
        $params = [
            'X-JOS-App-Key' => 'app-key',
            'X-JOS-Timestamp' => '1711111111000',
            'page' => '1',
        ];

        $expected = strtoupper(md5(
            'secret'
            . 'X-JOS-App-Keyapp-key'
            . 'X-JOS-Timestamp1711111111000'
            . 'page1'
            . 'secret'
        ));

        $this->assertSame($expected, SignatureCalculator::calculateSignature('secret', $params));
    }
}
