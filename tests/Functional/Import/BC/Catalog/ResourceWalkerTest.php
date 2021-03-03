<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Import\BC\Catalog;

use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\ProductResponse;
use Mrself\Mrcommerce\Import\BC\Catalog\ResourceWalkerOptions;
use Mrself\Mrcommerce\Import\BC\ResourceWalker;
use Mrself\Mrcommerce\Tests\Helpers\TestCase;

class ResourceWalkerTest extends TestCase
{
    /**
     * @var ResourceWalker
     */
    private $walker;

    public function testItDoesNotCallCallbackIfThereAreNoResourcesFound()
    {
        $this->apiMock->expects($this->once())
            ->method('getProducts')
            ->willReturn(new ProductResponse([]));

        $this->walker->configureOptions(function (ResourceWalkerOptions $options) {
            // Set null because callback should not be called in this test
            $options->callback = null;
            $options->byOne = true;
            $options->apiMethod = 'getProducts';
        });
        $this->walker->walk();
    }

    public function testItUsesIncludeResourcesOption()
    {
        $this->apiMock->expects($this->once())
            ->method('getProducts')
            ->with([
                'page' => 1,
                'limit' => ResourceWalker::MAX_RESOURCE_LIMIT,
                'include' => ['images', 'options', 'variants'],
            ])
            ->willReturn(new ProductResponse([]));

        $this->walker->configureOptions(function (ResourceWalkerOptions $options) {
            // Set null because callback should not be called in this test
            $options->callback = null;
            $options->byOne = true;
            $options->apiMethod = 'getProducts';
            $options->includeResources = [
                ResourceWalkerOptions::INCLUDE_RESOURCE_IMAGES,
                ResourceWalkerOptions::INCLUDE_RESOURCE_OPTIONS,
                ResourceWalkerOptions::INCLUDE_RESOURCE_VARIANTS,
            ];
        });
        $this->walker->walk();
    }

    public function testItProcessesOneFoundProductWithByOneOption()
    {
        $this->apiMock->expects($this->exactly(2))
            ->method('getProducts')
            ->willReturnOnConsecutiveCalls(
                new ProductResponse([
                    'data' => [
                        new Product(['id' => 1])
                    ]
                ]),
                new ProductResponse([
                    'data' => []
                ])
            );

        $callbackParam = null;
        $callback = function ($param) use (&$callbackParam) {
            $callbackParam = $param;
        };

        $this->walker->configureOptions(function (ResourceWalkerOptions $options) use ($callback) {
            // Set null because callback should not be called in this test
            $options->callback = $callback;
            $options->byOne = true;
            $options->apiMethod = 'getProducts';
        });
        $this->walker->walk();

        $this->assertEquals(1, $callbackParam->getId());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->walker = $this->container->get(ResourceWalker::class);
    }
}