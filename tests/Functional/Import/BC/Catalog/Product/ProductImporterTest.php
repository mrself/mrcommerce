<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Import\BC\Catalog\Product;

use BigCommerce\Api\v3\Model\Product;
use BigCommerce\Api\v3\Model\ProductResponse;
use Mrself\Mrcommerce\Import\BC\Catalog\Product\ProductImporter;
use Mrself\Mrcommerce\Tests\Helpers\TestCase;

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

        $this->importer->importAll();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->importer = $this->container->get(ProductImporter::class);
    }
}