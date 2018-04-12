<?php

namespace MelTests\Unit\Resources\HttpHelpers;

use Mel\Resources\HttpHelpers\GetAllHelper;
use Psr\Http\Message\ResponseInterface;
use MelTests\TestCase;

class GetAllHelperTest extends TestCase
{
    public function testShouldSendRequestToGetListOfTheResources()
    {
        $currenciesListJson = $this->getJsonFileContent('currencies/currencies-list');
        $client = \Mockery::mock('Http\Client\Common\HttpMethodsClient');
        $uriGenerator = \Mockery::mock(\Mel\Http\UriGenerator::class);

        $client->shouldReceive('get')
            ->once()
            ->with('/currencies')
            ->andReturn($this->createResponse($currenciesListJson));

        $uriGenerator->shouldReceive('resolveEndPointPath')
            ->once()
            ->with('/currencies', [], [])
            ->andReturn('/currencies');


        $getAllHelper = new GetAllHelperStub();

        $getAllHelper->setHttpClient($client);
        $getAllHelper->setUriGenerator($uriGenerator);

        $resourcesList = $getAllHelper->getAll();

        $expectedArray = json_decode($currenciesListJson, true);
        $this->assertEquals($expectedArray, $resourcesList);
    }
}

class GetAllHelperStub
{
    use GetAllHelper;

    public $paths = [
        'get-all' => '/currencies',
    ];

    public function hydrate(ResponseInterface $response)
    {
        return json_decode($response->getBody()->__toString(), true);
    }
}
