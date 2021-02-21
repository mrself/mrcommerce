<?php declare(strict_types=1);

namespace Mrself\Mrcommerce;

use BigCommerce\Api\v3\Configuration;

class ClientFactory
{
    /**
     * @var Configuration
     */
    private Configuration $config;

    public function __construct(string $host, string $accessToken, string $clientId)
    {
        $config = new Configuration();
        $config->setHost($host);
        $config->setAccessToken($accessToken);
        $config->setClientId($clientId);
        $this->config = $config;
    }

    public function __invoke()
    {
        return $this->config;
    }
}