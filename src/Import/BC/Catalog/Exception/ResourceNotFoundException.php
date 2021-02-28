<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Catalog\Exception;

use Mrself\Mrcommerce\MrcommerceException;

class ResourceNotFoundException extends MrcommerceException
{
    /**
     * @var int
     */
    private $bcId;

    public function __construct(int $bcId)
    {
        $this->bcId = $bcId;

        parent::__construct('Can not find a resource by id: ' . $bcId);
    }

    /**
     * @return int
     */
    public function getBcId(): int
    {
        return $this->bcId;
    }
}