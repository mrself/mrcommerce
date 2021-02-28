<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Import\BC\Catalog\Product;

use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\ProductResponse;
use Mrself\Mrcommerce\Import\BC\Catalog\ArrayImportProcessor;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\ResourceImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Product\ProductImporter;
use Mrself\Mrcommerce\Tests\Helpers\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProductImporterTest extends TestCase
{
    /**
     * @var ProductImporter
     */
    private $importer;

    public function testBase()
    {
        $this->apiMock->expects($this->exactly(2))
            ->method('getProducts')
            ->willReturnOnConsecutiveCalls(
                new ProductResponse([
                    'data' => [
                        new Product(['id' => 1])
                    ],
                ]),
                new ProductResponse([
                    'data' => [],
                ])
            );

        $processor = new ArrayImportProcessor();
        $this->importer->setImportProcessor($processor);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->addListener(ResourceImportedEvent::NAME, function (ResourceImportedEvent $event) {
            $this->assertEquals(1, $event->getBcResource()->getId());
        });

        $this->importer->importAll();

        $this->assertTrue($processor->hasImportedById(1));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->importer = $this->container->get(ProductImporter::class);
    }
}