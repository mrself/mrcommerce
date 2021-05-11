<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\BC;

use BigCommerce\Api\v3\ApiException;
use Symfony\Component\HttpFoundation\Response;

class ClientHelper
{
    public static function doRequest(callable $requestFn)
    {
        try {
            return $requestFn();
        } catch (ApiException $e) {
            if ($e->getCode() === Response::HTTP_TOO_MANY_REQUESTS) {
                $timeout = $e->getResponseHeaders()['X-Rate-Limit-Time-Reset-Ms'];
                $timeout = (int) ceil($timeout / 60);
                sleep($timeout);
                return $requestFn();
            }

            throw $e;
        }
    }
}