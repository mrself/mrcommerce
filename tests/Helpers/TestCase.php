<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Helpers;

use BigCommerce\Api\v3\Api\CatalogApi;
use League\Container\Container;
use Mrself\Mrcommerce\DependencyInjection\ContainerConfiguration;

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->apiMock = $this->createMock(CatalogApi::class);

        $configuration = (new ContainerConfiguration());
        $configuration->setOuterMap([
            'mr_bigcommerce.host' => 'bc_host',
            'mr_bigcommerce.access_token' => 'bc_access_token',
            'mr_bigcommerce.client_id' => 'bc_client_id',
            CatalogApi::class => $this->apiMock,
        ]);
        $this->container = $configuration->register()->getContainer();
    }
}