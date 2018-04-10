<?php

namespace MelTests\Unit\Http;

use Mel\Http\UriGenerator;
use Psr\Http\Message\UriInterface;
use MelTests\TestCase;

class UriGeneratorTest extends TestCase
{
    /**
     * @var UriGenerator
     */
    protected $uriGenerator;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->uriGenerator = new UriGenerator($this->getMel());
    }

    public function testShouldCreateUriInstanceWithCorrectlyEndpointAndParametersToUseInApiAccess()
    {
        $uri = $this->uriGenerator->createApiUri(
            'questions/search',
            ['item_id' => 'MLA608007087', 'access_token' => $this->accessToken]
        );

        $this->assertInstanceOf(UriInterface::class, $uri);

        $this->assertEquals(
            sprintf(
                'https://api.mercadolibre.com/questions/search?item_id=MLA608007087&access_token=%1$s',
                $this->accessToken
            ),
            $uri->__toString()
        );
    }

    public function testShouldCreateUriInstanceWithCorrectlyEndpointAndParametersToUseInOAuth()
    {
        $uri = $this->uriGenerator->createOAuthUri(
            'authorization',
            ['response_type' => 'code', 'client_id' => $this->appId, 'redirect_uri' => $this->redirectUri]
        );

        $this->assertInstanceOf(UriInterface::class, $uri);

        $this->assertEquals(
            sprintf(
                'https://auth.mercadolivre.com.br/authorization?%1$s',
                http_build_query([
                    'response_type' => 'code',
                    'client_id'     => $this->appId,
                    'redirect_uri'  => $this->redirectUri,
                ])
            ),
            $uri->__toString()
        );
    }

    public function testAddQueryToUriUsingArrayOfTheParameters()
    {
        $uri = $this->uriGenerator->addQuery(
            $this->uriGenerator->createApiUri(),
            [
                'key1' => 'value1',
                'key2' => 'value2',
            ]
        );

        $this->assertEquals('https://api.mercadolibre.com?key1=value1&key2=value2', $uri->__toString());
    }

    public function testShouldCreateUriUsingEndpointPathWithDynamicSegments()
    {
        $uri = $this->uriGenerator->resolveEndPointPath(
            '/endpoint/{id}/path/{name}',
            ['id' => 42, 'name' => 'nick'],
            ['q' => 'query-value']
        );

        $expected = '/endpoint/42/path/nick?q=query-value';

        $this->assertEquals($expected, $uri->__toString());
    }
}
