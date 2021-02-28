<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Hooks;

class RequestParserException extends \Exception
{
    public const DECODE_ERROR = 0;
    public const HASH_DEFINE_ERROR = 1;
    public const HASH_MISMATCH_ERROR = 2;
    public const DEFINE_SCOPE_ERROR = 3;
    public const SCOPE_MISMATCH_ERROR = 4;

    public static function cannotDecodeRequestContent()
    {
        return new static('Can not decode request content', static::DECODE_ERROR);
    }

    public static function cannotDefineStoreHash()
    {
        return new static('Can not define store hash', static::HASH_DEFINE_ERROR);
    }
    
    public static function storeHashMismatch(string $appStoreHash, string $requestStoreHash)
    {
        return new static(
            "App store hash '$appStoreHash' does not match the store '$requestStoreHash' passed via request",
            static::HASH_MISMATCH_ERROR
        );
    }

    public static function cannotDefineScope($scope)
    {
        return new static('Can not define scope from "' . $scope . '"', static::DEFINE_SCOPE_ERROR);
    }

    public static function scopesMismatch(string $currentScope, string $validScope)
    {
        return new static(
            "The defined scope $currentScope does not match the valid scope $validScope",
            static::SCOPE_MISMATCH_ERROR
        );
    }
}