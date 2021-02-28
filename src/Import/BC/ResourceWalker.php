<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC;

use Mrself\Mrcommerce\Import\BC\Catalog\ResourceWalkerOptions;

class ResourceWalker
{
    public const MAX_RESOURCE_LIMIT = 250;

    /**
     * @var int
     */
    private $startPage = 1;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var int
     */
    private $resourceLimit = ResourceWalker::MAX_RESOURCE_LIMIT;

    /**
     * @var ResourceWalkerOptions
     */
    private $options;

    /**
     * @var integer
     */
    private $index = 0;

    public function __construct(ResourceWalkerOptions $options)
    {
        $this->options = $options;
    }

    public function configureOptions(callable $callback)
    {
        $callback($this->options);
    }

    public function walk()
    {
        do {
            $items = $this->getPage();
            if (!$items) {
                break;
            }

            if ($this->processItems($items) === false) {
                break;
            }
        } while (true);
    }

    protected function processItems(array $items): bool
    {
        if ($this->options->byOne) {
            return $this->walkItems($items);
        }

        $result = $this->applyCallback($items);
        if ($result === null) {
            return true;
        }
        return $result;
    }

    private function applyCallback($params)
    {
        return ($this->options->callback)($params, $this->index++);
    }

    private function walkItems(array $items): bool
    {
        foreach ($items as $item) {
            if ($this->applyCallback($item) === false) {
                return false;
            }
        }

        return true;
    }

    private function getPage()
    {
        $method = $this->options->apiMethod;
        $params = $this->options->resourceParams + [
            'page' => $this->definePage(),
            'limit' => $this->resourceLimit
        ];

        $response = $this->options->client->$method(array_merge($params, $this->options->queryParams));
        if (!$response || !$response->getData()) {
            return null;
        }

        return $response->getData();
    }

    protected function definePage(): int
    {
        if ($this->currentPage) {
            return ++$this->currentPage;
        }

        return $this->currentPage = $this->startPage;
    }
}