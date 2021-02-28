<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\BC;

use Bigcommerce\Api\Client as Bigcommerce;

class BigcommerceV2Configurator
{
    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $storeHash;

    public function __construct(string $clientId, string $token, string $storeHash)
    {
        $this->clientId = $clientId;
        $this->token = $token;
        $this->storeHash = $storeHash;
    }

    public function configure()
    {
        Bigcommerce::configure(array(
            'client_id' => $this->clientId,
            'auth_token' => $this->token,
            'store_hash' => $this->storeHash,
        ));
    }
}