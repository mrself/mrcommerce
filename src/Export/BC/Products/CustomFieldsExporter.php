<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Export\BC\Products;

use BigCommerce\Api\v3\Model\CustomField;
use Mrself\Mrcommerce\BC\Bigcommerce;
use Psr\Log\LoggerInterface;

class CustomFieldsExporter
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Bigcommerce
     */
    private $bigcommerce;

    public function __construct(LoggerInterface $logger, Bigcommerce $bigcommerce)
    {
        $this->logger = $logger;
        $this->bigcommerce = $bigcommerce;
    }

    public function exportByMap(array $fieldsMap)
    {
        $requestParams = [];
        foreach ($fieldsMap as $productId => $customFields) {
            $fieldsForRequest = $this->defineFieldsForRequest($productId, $customFields);
            if (!$fieldsForRequest) {
                continue;
            }

            $requestParams[] = [
                'id' => $productId,
                'custom_fields' => $fieldsForRequest,
            ];
        }

        if (!$requestParams) {
            return;
        }

        $this->bigcommerce->updateCustomFieldsInBatch($requestParams);
    }

    public function defineFieldsForRequest(int $productId, array $newFields): array
    {
        $existingFields = $this->bigcommerce->getCustomFields($productId);
        $this->removeExtraFields($productId, $existingFields, $newFields);
        return $this->findFieldsToCreate($newFields, $existingFields);

    }

    public function removeExtraFields(int $productId, array $existingFields, array $newFields)
    {
        $extraFields = $this->findExtras($newFields, $existingFields);
        $this->removeFields($productId, $extraFields);
    }

    private function removeFields(int $productId, array $fields)
    {
        foreach ($fields as $field) {
            $this->bigcommerce->deleteCustomFieldByObject($productId, $field);
        }
    }

    public function findFieldsToCreate(array $newFields, array $existingFields): array
    {
        $fieldsToCreate = [];
        foreach ($newFields as $newField) {
            $foundField = $this->findInExisting($newField, $existingFields);
            if (!$foundField) {
                $fieldsToCreate[] = $newField;
            }
        }
        return $fieldsToCreate;
    }

    /**
     * @param array $newField
     * @param CustomField[] $existingFields
     * @return false
     */
    private function findInExisting(array $newField, array $existingFields): bool
    {
        foreach ($existingFields as $existingField) {
            if ($existingField->getName() === $newField['name']) {
                if ($existingField->getValue() === $newField['value']) {
                    return true;
                }
            }
        }

        return false;
    }

    private function findExtras(array $newFields, array $existingFields): array
    {
        $extraFields = [];
        foreach ($existingFields as $existingField) {
            $foundField = $this->findFieldInNew($existingField, $newFields);
            if (!$foundField) {
                $extraFields[] = $existingField;
            }
        }

        return $extraFields;
    }

    private function findFieldInNew(CustomField $field, array $newFields)
    {
        foreach ($newFields as $newField) {
            if ($newField['name'] === $field->getName()) {
                if ($newField['value'] === $field->getValue()) {
                    return $field;
                }
            }
        }

        return false;
    }

}