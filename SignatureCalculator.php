<?php
namespace com\jd\openapi\apiClient;

use com\jd\openapi\apiClient\ApiException;
use GuzzleHttp\Psr7\Utils;

class SignatureCalculator
{
    public static function parameterConvert(array $systemParam, array $queryParam, $bodyParam, array $pathParam)
    {
        $paramMap = [];
        $paramMap['X-JOS-App-Key'] = $systemParam['appKey'];
        if (isset($systemParam['accessToken'])) {
            $paramMap['X-JOS-Access-Token'] = $systemParam['accessToken'];
        }
        $paramMap['X-JOS-Timestamp'] = $systemParam['timestamp'];
        if (isset($systemParam['signMethod'])) {
            $paramMap['X-JOS-Sign-Method'] = $systemParam['signMethod'];
        }
        if (!empty($queryParam)) {
            foreach ($queryParam as $key => $value) {
                $paramMap[$key] = $value;
            }
        }
        if (!empty($pathParam)) {
            foreach ($pathParam as $key => $value) {
                $paramMap[$key] = $value;
            }
        }
        if ($bodyParam !== null && !empty($bodyParam) && $bodyParam !== '{}') {
            $paramMap['body'] = $bodyParam;
        }

        ksort($paramMap);


        return $paramMap;
    }

    public static function calculateSignature($secret, array $paramMap)
    {
        try {
            return self::byte2Hex(self::md5Calculate($secret, self::signature4OpenApi($paramMap, $secret)));
        } catch (\Exception $e) {
            throw new ApiException("Failed to calculate md5 signature", $e);
        }
    }

    protected static function md5Calculate($secret, $preSignStr) {
        return md5($preSignStr, true);
    }

    private static function byte2Hex($bytes) {
        $hex = '';
        foreach (str_split($bytes) as $byte) {
            $hex .= sprintf("%02X", ord($byte)); // ʹ�� %02X �Է��ش�д
        }
        return $hex;
    }

    protected static function signature4OpenApi(array $paramsMap, $secret)
    {
        $preSignStr = $secret;
        foreach ($paramsMap as $key => $value) {
            $preSignStr .= $key . $value;
        }
        $preSignStr .= $secret;
        return $preSignStr;
    }
}