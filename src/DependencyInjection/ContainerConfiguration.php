<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\DependencyInjection;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\ApiClient;
use BigCommerce\Api\v3\Configuration;
use League\Container\Container;
use Mrself\Mrcommerce\Import\BC\Catalog\ImportersManager;
use Mrself\Mrcommerce\Import\BC\Catalog\Product\ProductImporter;
use Mrself\Mrcommerce\Import\BC\Catalog\ResourceWalkerOptions;
use Mrself\Mrcommerce\Import\BC\ResourceWalker;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ContainerConfiguration
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @var array
     */
    private $outerMap = [];

    public function setOuterMap(array $outerMap)
    {
        $this->outerMap = $outerMap;
    }

    public function register()
    {
        $this->container = $container = new Container();

        foreach ($this->outerMap as $class => $service) {
            $container->share($class, $service);
        }

        foreach ($this->getServicesMap() as $class => $service) {
            $container->share($class, $service);
        }

        foreach ($this->getServicesWithArguments() as $class => $arguments) {
            $service = $container->share($class, $class);
            foreach ($arguments as $argument) {
                $service->addArgument($argument);
            }
        }

        return $this;
    }

    public function getContainer()
    {
        return $this->container;
    }

    private function getServicesMap()
    {
        return [
            CatalogApi::class => $this->makeCatalogApi(),
            EventDispatcherInterface::class => new EventDispatcher(),
        ];
    }

    private function getServicesWithArguments(): array
    {
        return [
            ProductImporter::class => [
                CatalogApi::class,
                ResourceWalker::class,
                EventDispatcherInterface::class
            ],

            ResourceWalkerOptions::class => [
                CatalogApi::class
            ],

            ResourceWalker::class => [
                ResourceWalkerOptions::class
            ],

            ImportersManager::class => [
                ProductImporter::class,
            ]
        ];
    }

    private function makeCatalogApi()
    {
        if ($catalogApi = $this->container->get(CatalogApi::class)) {
            return $catalogApi;
        }

        $config = new Configuration();
        $config->setHost($this->container->get('mr_bigcommerce.host'));
        $config->setAccessToken($this->container->get('mr_bigcommerce.access_token'));
        $config->setClientId($this->container->get('mr_bigcommerce.client_id'));
        $apiClient = new ApiClient($config);
        return new CatalogApi($apiClient);
    }
}