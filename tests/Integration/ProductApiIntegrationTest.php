<?php

declare(strict_types=1);

namespace JdSpApiSdk\Tests\Integration;

use com\jd\openapi\apiClient\ApiException;
use com\jd\openapi\apiClient\sp\product\v0\api\ProductsApi;
use com\jd\openapi\apiClient\sp\product\v0\api\SkusApi;
use com\jd\openapi\apiClient\sp\product\v0\api\SizeTemplatesApi;
use com\jd\openapi\apiClient\sp\product\v0\model\ListProductsRequest;
use com\jd\openapi\apiClient\sp\product\v0\model\ListProductsResponse;
use com\jd\openapi\apiClient\sp\product\v0\model\ListSizeTemplatesRequest;
use com\jd\openapi\apiClient\sp\product\v0\model\ListSizeTemplatesResponse;
use com\jd\openapi\apiClient\sp\product\v0\model\ListSkusRequest;
use com\jd\openapi\apiClient\sp\product\v0\model\ListSkusResponse;

final class ProductApiIntegrationTest extends IntegrationTestCase
{
    /**
     * @group live
     */
    public function testListProductsReturnsSuccessfulResponse(): void
    {
        $api = new ProductsApi($this->createConfiguration());
        $request = new ListProductsRequest([
            'page' => 1,
            'page_size' => 1,
        ]);

        $response = $this->executeLiveRequest(
            static fn() => $api->listProducts($request)
        );

        $this->assertInstanceOf(ListProductsResponse::class, $response);
        $this->assertTrue(
            (bool) $response->getSuccess(),
            $this->formatErrorList($response->getErrorList())
        );
        $this->assertIsArray($response->getData());
        $this->assertNotNull($response->getPaginationData());
    }

    /**
     * @group live
     */
    public function testListSkusReturnsSuccessfulResponse(): void
    {
        $api = new SkusApi($this->createConfiguration());
        $request = new ListSkusRequest([
            'page' => 1,
            'page_size' => 1,
        ]);

        $response = $this->executeLiveRequest(
            static fn() => $api->listSkus($request)
        );

        $this->assertInstanceOf(ListSkusResponse::class, $response);
        $this->assertTrue(
            (bool) $response->getSuccess(),
            $this->formatErrorList($response->getErrorList())
        );
        $this->assertIsArray($response->getData());
    }

    /**
     * @group live
     */
    public function testListSizeTemplatesReturnsSuccessfulResponse(): void
    {
        $api = new SizeTemplatesApi($this->createConfiguration());
        $request = new ListSizeTemplatesRequest([
            'page' => 1,
            'page_size' => 1,
        ]);

        $response = $this->executeLiveRequest(
            static fn() => $api->listSizeTemplates($request)
        );

        $this->assertInstanceOf(ListSizeTemplatesResponse::class, $response);
        $this->assertTrue(
            (bool) $response->getSuccess(),
            $this->formatErrorList($response->getErrorList())
        );
        $this->assertIsArray($response->getData());
    }

    private function formatErrorList(?array $errorList): string
    {
        if (empty($errorList)) {
            return 'JD product API returned success=false with an empty error list.';
        }

        $messages = [];
        foreach ($errorList as $error) {
            if (is_object($error) && method_exists($error, 'getMessage')) {
                $messages[] = (string) $error->getMessage();
                continue;
            }

            $messages[] = (string) json_encode($error, JSON_UNESCAPED_UNICODE);
        }

        return 'JD product API returned success=false: ' . implode(' | ', $messages);
    }

    /**
     * @return mixed
     */
    private function executeLiveRequest(callable $request)
    {
        try {
            return $request();
        } catch (ApiException $exception) {
            if ($this->isCredentialOrPermissionIssue($exception)) {
                self::markTestSkipped(
                    'Live JD credentials are not authorized for this API: ' . $exception->getMessage()
                );
            }

            throw $exception;
        }
    }

    private function isCredentialOrPermissionIssue(ApiException $exception): bool
    {
        if (in_array($exception->getCode(), [401, 403], true)) {
            return true;
        }

        $responseBody = $exception->getResponseBody();
        if (!is_string($responseBody) || $responseBody === '') {
            return false;
        }

        $payload = json_decode($responseBody, true);
        if (!is_array($payload) || !isset($payload['errorList']) || !is_array($payload['errorList'])) {
            return false;
        }

        foreach ($payload['errorList'] as $error) {
            $code = isset($error['code']) ? (string) $error['code'] : '';
            $message = isset($error['message']) ? (string) $error['message'] : '';

            if (in_array($code, ['99904010003', '10199001002'], true)) {
                return true;
            }

            if (str_contains($message, '令牌无效') || str_contains($message, '权限不足')) {
                return true;
            }
        }

        return false;
    }
}
