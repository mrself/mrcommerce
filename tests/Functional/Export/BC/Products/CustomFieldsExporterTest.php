<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Export\BC\Products;

use BigCommerce\Api\v3\Model\CustomField;
use Mrself\Mrcommerce\BC\Bigcommerce;
use Mrself\Mrcommerce\Export\BC\Products\CustomFieldsExporter;
use Mrself\Mrcommerce\Tests\Helpers\TestCase;

class CustomFieldsExporterTest extends TestCase
{
    /**
     * @var CustomFieldsExporter
     */
    private $exporter;

    public function testItUpdatesCustomFieldsInBatchByMap()
    {
        $productId = 1;

        $this->bigcommerceMock
            ->expects($this->once())
            ->method('getCustomFields')
            ->with($productId)
            ->willReturn([]);

        $customField = ['name' => 'Name', 'value' => 'Value'];
        $this->bigcommerceMock
            ->expects($this->once())
            ->method('updateCustomFieldsInBatch')
            ->with([
                [
                    'id' => $productId,
                    'custom_fields' => [$customField]
                ]
            ]);

        $this->exporter->exportByMap([
            $productId => [$customField]
        ]);
    }

    public function testItRemovesExtraFields()
    {
        $productId = 1;

        $this->bigcommerceMock
            ->expects($this->once())
            ->method('getCustomFields')
            ->with($productId)
            ->willReturn([
                new CustomField(['id' => 2, 'name' => 'Name1', 'value' => 'Value1'])
            ]);

        $this->bigcommerceMock
            ->expects($this->once())
            ->method('deleteCustomFieldByObject')
            ->with(
                $this->callback(function ($productId) {
                    return $productId === 1;
                }),
                $this->callback(function ($field) {
                    return $field->getId() === 2;
                })
            );

        $customField = ['name' => 'Name', 'value' => 'Value'];
        $this->bigcommerceMock
            ->expects($this->once())
            ->method('updateCustomFieldsInBatch')
            ->with([
                [
                    'id' => $productId,
                    'custom_fields' => [$customField]
                ]
            ]);

        $this->exporter->exportByMap([
            $productId => [$customField]
        ]);
    }

    protected function setUp(): void
    {
        $this->containerOuterMap = [
            Bigcommerce::class => $this->createBigcommerceMock(),
        ];

        parent::setUp();

        $this->exporter = $this->container->get(CustomFieldsExporter::class);
    }
}