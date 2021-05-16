<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\DependencyInjection;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Configuration;
use League\Container\Container;
use Mrself\Mrcommerce\BC\ApiClient;
use Mrself\Mrcommerce\BC\Bigcommerce;
use Mrself\Mrcommerce\BC\BigcommerceV2Configurator;
use Mrself\Mrcommerce\Export\BC\Products\CustomFieldsExporter;
use Mrself\Mrcommerce\Import\BC\Catalog\ImportersManager;
use Mrself\Mrcommerce\Import\BC\Catalog\Product\ProductImporter;
use Mrself\Mrcommerce\Import\BC\Catalog\ResourceWalkerOptions;
use Mrself\Mrcommerce\Import\BC\Hooks\HooksManager;
use Mrself\Mrcommerce\Import\BC\Hooks\RequestParser;
use Mrself\Mrcommerce\Import\BC\ResourceWalker;
use Psr\Log\LoggerInterface;
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

        $container->add(ResourceWalker::class)
            ->addArgument(ResourceWalkerOptions::class);

        $container->add(ResourceWalkerOptions::class)
            ->addArgument(CatalogApi::class);

        $container->share(BigcommerceV2Configurator::class)
            ->addArguments([
                $container->get('mr_bigcommerce.client_id'),
                $container->get('mr_bigcommerce.access_token'),
                $container->get('mr_bigcommerce.store_hash'),
            ]);

        $container->share(HooksManager::class)
            ->addArguments([
                BigcommerceV2Configurator::class,
                $container->get('mr_bigcommerce.hooks_dest_url'),
            ]);

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
            RequestParser::class => new RequestParser(),
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

            ImportersManager::class => [
                ProductImporter::class,
            ],

            Bigcommerce::class => [
                ApiClient::class,
                CatalogApi::class,
                LoggerInterface::class,
            ],

            CustomFieldsExporter::class => [
                LoggerInterface::class,
                Bigcommerce::class,
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