<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\BC;

use BigCommerce\Api\v3\ApiException;
use Symfony\Component\HttpFoundation\Response;

class ApiClient extends \BigCommerce\Api\v3\ApiClient
{
    public function callApi($resourcePath, $method, $queryParams, $postData, $headerParams, $responseType = null, $endpointPath = null)
    {
        try {
            return parent::callApi($resourcePath, $method, $queryParams, $postData, $headerParams, $responseType, $endpointPath);
        } catch (ApiException $e) {
            if ($e->getCode() === Response::HTTP_TOO_MANY_REQUESTS) {
                $timeout = $e->getResponseHeaders()['X-Rate-Limit-Time-Reset-Ms'];
                $timeout = (int) ceil($timeout / 60);
                sleep($timeout);
                return parent::callApi($resourcePath, $method, $queryParams, $postData, $headerParams, $responseType, $endpointPath);
            }

            throw $e;
        }
    }
}