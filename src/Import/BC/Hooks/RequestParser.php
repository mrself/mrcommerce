<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Import\BC\Hooks;

class RequestParser
{
    public function parse(string $requestContent, string $validScope, string $validStoreHash, string $resource): ParsedRequest
    {
        $content = json_decode($requestContent);
        $parsedRequest = new ParsedRequest();

        if (null === $content || is_string($content)) {
            throw RequestParserException::cannotDecodeRequestContent();
        }

        $storeHash = $this->retrieveStoreHash($content->producer);
        if (null === $storeHash) {
            throw RequestParserException::cannotDefineStoreHash();
        }

        $parsedRequest->storeHash = $storeHash;

        if ($validStoreHash !== $storeHash) {
            throw RequestParserException::storeHashMismatch($validStoreHash, $storeHash);
        }

        $scopeParts = explode('store/' . $resource . '/', $content->scope);
        if (count($scopeParts) !== 2) {
            throw RequestParserException::cannotDefineScope($content->scope);
        }

        $parsedRequest->scope = $scopeParts[1];

        if ($scopeParts[1] !== $validScope) {
            throw RequestParserException::scopesMismatch($content->scope, $validScope);
        }

        $parsedRequest->id = $content->data->id;

        return $parsedRequest;
    }

    private function retrieveStoreHash($producer): ?string
    {
        if (!is_string($producer)) {
            return null;
        }

        $parts = explode('/', $producer);
        if (count($parts) === 2) {
            return $parts[1];
        }
        return null;
    }
}