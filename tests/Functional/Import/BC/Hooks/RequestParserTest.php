<?php declare(strict_types=1);

namespace Mrself\Mrcommerce\Tests\Functional\Import\BC\Hooks;

use Mrself\Mrcommerce\Import\BC\Hooks\ParsedRequest;
use Mrself\Mrcommerce\Import\BC\Hooks\RequestParser;
use Mrself\Mrcommerce\Import\BC\Hooks\RequestParserException;
use Mrself\Mrcommerce\Tests\Helpers\TestCase;

class RequestParserTest extends TestCase
{
    /**
     * @var RequestParser
     */
    private $parser;

    public function testItParsesRequestContent()
    {
        $validContent = json_encode($this->getValidContent());

        $parsedRequest = $this->parser->parse(
            $validContent,
            'created',
            $this->container->get('mr_bigcommerce.host'),
            'product'
        );

        $this->assertInstanceOf(ParsedRequest::class, $parsedRequest);
    }

    public function testItThrowsIfCanNotDecodeContent()
    {
        $this->expectExceptionCode(RequestParserException::DECODE_ERROR);

        $this->parser->parse(
            'invalidJson',
            'created',
            $this->container->get('mr_bigcommerce.host'),
            'product'
        );
    }

    public function testItThrowsIfCanNotDefineStoreHash()
    {
        $this->expectExceptionCode(RequestParserException::HASH_DEFINE_ERROR);

        $content = $this->getValidContent();
        $content['producer'] = 'invalid-hash';

        $this->parser->parse(
            json_encode($content),
            'created',
            $this->container->get('mr_bigcommerce.host'),
            'product'
        );
    }

    public function testItThrowsIfAppStoreHashDoesNotMatchDefinedHash()
    {
        $this->expectExceptionCode(RequestParserException::HASH_MISMATCH_ERROR);

        $content = $this->getValidContent();
        $content['producer'] = 'stores/invalid-hash';

        $this->parser->parse(
            json_encode($content),
            'created',
            $this->container->get('mr_bigcommerce.host'),
            'product'
        );
    }

    public function testItThrowsIfCanNotDefineScope()
    {
        $this->expectExceptionCode(RequestParserException::DEFINE_SCOPE_ERROR);

        $content = $this->getValidContent();
        $content['scope'] = 'store/product-invalid-scope';

        $this->parser->parse(
            json_encode($content),
            'created',
            $this->container->get('mr_bigcommerce.host'),
            'product'
        );
    }

    public function testItThrowsIfScopeDoesNotMatchProvidedScope()
    {
        $this->expectExceptionCode(RequestParserException::SCOPE_MISMATCH_ERROR);

        $content = $this->getValidContent();
        $content['scope'] = 'store/product/non-create-scope';

        $this->parser->parse(
            json_encode($content),
            'created',
            $this->container->get('mr_bigcommerce.host'),
            'product'
        );
    }

    private function getValidContent(): array
    {
        return [
            'scope' => 'store/product/created',
            'data' => [
                'id' => 1,
                'type' => 'product'
            ],
            'producer' => 'stores/' . $this->container->get('mr_bigcommerce.host'),
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = $this->container->get(RequestParser::class);
    }
}