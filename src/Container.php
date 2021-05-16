<?php declare(strict_types=1);

namespace Mrself\Mrcommerce;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Configuration;
use Mrself\Mrcommerce\BC\ApiClient;

class Container
{
    private static Container $instance;

    protected array $items = [];

    public function __construct(array $options)
    {
        if (!$options) {
            $options = $this->retrieveOptionsFromEnv();
        }

        $config = new Configuration();
        $config->setHost($options['host']);
        $config->setAccessToken($options['accessToken']);
        $config->setClientId($options['clientId']);
        $this->items['mrcommerce.bc.config'] = $config;
        $this->items['mrcommerce.bc.client'] = new ApiClient($config);
        $this->items['mrcommerce.bc.catalog'] = new CatalogApi($this->items['mrcommerce.bc.client']);

        static::$instance = $this;
    }

    public static function getInstance()
    {
        return static::$instance;
    }

    public function loadEnv()
    {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();
    }

    public function register()
    {

    }

    public function boot()
    {

    }

    private function retrieveOptionsFromEnv(): array
    {
        return [
            'host' => $_ENV['MRCOMMERCE_HOST'],
            'accessToken' => $_ENV['MRCOMMERCE_ACCESS_TOKEN'],
            'clientId' => $_ENV['MRCOMMERCE_CLIENT_ID'],
        ];
    }
}