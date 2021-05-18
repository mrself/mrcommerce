<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\BC;

use BigCommerce\Api\v3\Api\CatalogApi;
use BigCommerce\Api\v3\Model\CustomField;
use Mrself\Mrcommerce\Import\BC\ResourceWalker;
use Psr\Log\LoggerInterface;

class Bigcommerce
{
    /**
     * @var ApiClient
     */
    private $client;

    /**
     * @var CatalogApi
     */
    private $catalogApi;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ApiClient $client, CatalogApi $catalogApi, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->catalogApi = $catalogApi;
        $this->logger = $logger;
    }

    public function updateCustomFieldsInBatch(array $params)
    {
        $this->logger->info('Updating products with new custom fields', [
            'data' => $params,
        ]);

        $this->catalogApi->getApiClient()->callApi(
            '/catalog/products',
            'PUT',
            [],
            $params,
            ['Content-Type' => 'application/json']
        );
    }

    public function updateProductCategoriesInBatch(array $params)
    {
        $this->logger->info('Updating products with new categories', [
            'data' => $params
        ]);

        $this->catalogApi->getApiClient()->callApi(
            '/catalog/products',
            'PUT',
            [],
            $params,
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * @param int $productId
     * @return CustomField[]
     * @throws \BigCommerce\Api\v3\ApiException
     */
    public function getCustomFields(int $productId): array
    {
        $this->logger->info('Fetching custom fields for the product #' . $productId);
        $customFields = $this->catalogApi->getCustomFields($productId, ['limit' => ResourceWalker::MAX_RESOURCE_LIMIT])->getData();
        $this->logger->info('Found ' . count($customFields) . ' custom fields for the product #' . $productId);

        return $customFields;
    }

    public function deleteCustomFieldByObject(int $productId, CustomField $field)
    {
        $this->logger->info('Removing custom field for the product #' . $productId);
        $this->catalogApi->deleteCustomFieldById($productId, $field->getId());
    }
}