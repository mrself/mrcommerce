<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Helpers;

use BigCommerce\Api\v3\Api\CatalogApi;
use League\Container\Container;
use Mrself\Mrcommerce\BC\Bigcommerce;
use Mrself\Mrcommerce\DependencyInjection\ContainerConfiguration;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var CatalogApi|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $apiMock;

    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $containerOuterMap = [];

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $bigcommerceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiMock = $this->createMock(CatalogApi::class);

        $configuration = (new ContainerConfiguration());

        $outerMap = array_merge($this->containerOuterMap, [
            'mr_bigcommerce.host' => 'bc_host',
            'mr_bigcommerce.access_token' => 'bc_access_token',
            'mr_bigcommerce.client_id' => 'bc_client_id',
            'mr_bigcommerce.store_hash' => 'store_hash',
            'mr_bigcommerce.hooks_dest_url' => 'store_hash',
            CatalogApi::class => $this->apiMock,
            LoggerInterface::class => new NullLogger(),
        ]);
        $configuration->setOuterMap($outerMap);

        $this->container = $configuration->register()->getContainer();
        $this->eventDispatcher = $this->container->get(EventDispatcherInterface::class);
    }

    protected function createBigcommerceMock()
    {
        return $this->bigcommerceMock = $this->createMock(Bigcommerce::class);
    }
}