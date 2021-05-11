<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Import\BC\Catalog\Product;

use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\ProductCollectionResponse;
use BigCommerce\Api\v3\Model\ProductResponse;
use Mrself\Mrcommerce\Import\BC\Catalog\AbsentEntitiesRemovingInterface;
use Mrself\Mrcommerce\Import\BC\Catalog\ArrayImportProcessor;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\BatchResourceImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\ResourceImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Event\ResourcesImportedEvent;
use Mrself\Mrcommerce\Import\BC\Catalog\Exception\RemoveAbsentMethodNotExistException;
use Mrself\Mrcommerce\Import\BC\Catalog\Exception\ResourceNotFoundException;
use Mrself\Mrcommerce\Import\BC\Catalog\ImportResult\ResourceImportResult;
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

        $this->eventDispatcher->addListener(
            BatchResourceImportedEvent::NAME,
            function (BatchResourceImportedEvent $event) {
                $this->assertInstanceOf(ResourceImportResult::class, $event->getResult());
            }
        );

        $this->importer->importAll();

        $this->assertTrue($processor->hasBatchImportedById(1));
    }

    public function testResourceIsSkippedIfImportProcessorTellsThatItShouldBeSkipped()
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
        $processor->setSkipAll(true);
        $this->importer->setImportProcessor($processor);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->addListener(ResourceImportedEvent::NAME, function (ResourceImportedEvent $event) {
            $this->assertEquals(1, $event->getBcResource()->getId());
        });

        $eventDispatched = false;
        $this->eventDispatcher->addListener(
            BatchResourceImportedEvent::NAME,
            function (BatchResourceImportedEvent $event) use (&$eventDispatched) {
                $result = $event->getResult();
                $this->assertInstanceOf(ResourceImportResult::class, $result);
                $this->assertFalse($result->isProcessed());
                $eventDispatched = true;
            }
        );

        $this->importer->importAll();
        $this->assertTrue($eventDispatched);
    }

    public function testImportByBcIds()
    {
        $this->apiMock->expects($this->exactly(2))
            ->method('getProducts')
            ->withConsecutive(
                [['id:in' => [1]]],
                [['id:in' => [2]]]
            )
            ->willReturnOnConsecutiveCalls(
                new ProductCollectionResponse([
                    'data' => [
                        new Product(['id' => 1])
                    ],
                ]),
                new ProductCollectionResponse([
                    'data' => [
                        new Product(['id' => 2])
                    ],
                ])
            );

        $processor = new ArrayImportProcessor();
        $this->importer->setImportProcessor($processor);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->addListener(ResourcesImportedEvent::NAME, function (ResourcesImportedEvent $event) {
            $this->assertCount(1, $event->getResources());
        });

        $this->importer->importByBcIds([1, 2], 1);

        $this->assertTrue($processor->hasBatchImportedById(1));
    }

    public function testImportByBcId()
    {
        $this->apiMock->expects($this->once())
            ->method('getProductById')
            ->with(1)
            ->willReturn(new ProductResponse([
                'data' => new Product(['id' => 1])
            ]));

        $processor = new ArrayImportProcessor();
        $this->importer->setImportProcessor($processor);

        /** @var EventDispatcher $dispatcher */
        $dispatcher = $this->container->get(EventDispatcherInterface::class);
        $dispatcher->addListener(ResourceImportedEvent::NAME, function (ResourceImportedEvent $event) {
            $this->assertEquals(1, $event->getBcResource()->getId());
        });

        $this->importer->importByBcId(1);

        $this->assertTrue($processor->hasImportedById(1));
    }

    public function testImportByBcIdThrowsIfCanNotFindResource()
    {
        $this->expectExceptionObject(new ResourceNotFoundException(1));

        $this->apiMock->expects($this->once())
            ->method('getProductById')
            ->with(1)
            ->willReturn(new ProductResponse([
                'data' => null
            ]));

        $this->importer->importByBcId(1);
    }

    public function testImporterRemovesAbsentEntitiesIfItHasRelativeConfig()
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

        $processor = new class extends ArrayImportProcessor implements AbsentEntitiesRemovingInterface {
            public function removeAbsentEntities()
            {
                $this->absentEntitiesRemoved = true;
            }

            public function resetIsImportedField()
            {
            }

        };
        $this->importer->setImportProcessor($processor);

        $this->importer->setRemoveAbsentEntities(true);
        $this->importer->importAll();

        $this->assertTrue($processor->absentEntitiesRemoved);
    }

    public function testImporterThrowsIfRemoveAbsentEntitiesDoesNotExist()
    {
        $this->markTestSkipped('The logic to check if absent entities should be removed was re-implemented with interfaces');

        $this->expectException(RemoveAbsentMethodNotExistException::class);

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

        $processor = $this->createImportProcessor();
        $this->importer->setRemoveAbsentEntities(true);
        $this->importer->importAll();

        $this->assertTrue($processor->absentEntitiesRemoved);
    }

    private function createImportProcessor(): ArrayImportProcessor
    {
        $processor = new ArrayImportProcessor();
        $this->importer->setImportProcessor($processor);

        return $processor;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->importer = $this->container->get(ProductImporter::class);
    }
}