<?php

namespace MelTests\Unit\Resources\HttpHelpers;

use Mel\Resources\HttpHelpers\GetByIdHelper;
use Psr\Http\Message\ResponseInterface;
use MelTests\TestCase;

class GetByIdHelperTest extends TestCase
{
    public function testShouldSendRequestToGetSingleObjectResource()
    {
        $currencyJson = $this->getJsonFileContent('currencies/single-currency');
        $client = \Mockery::mock(\Http\Client\Common\HttpMethodsClient::class);
        $uriGenerator = \Mockery::mock(\Mel\Http\UriGenerator::class);


        $client->shouldReceive('get')
            ->once()
            ->with('/currencies/CLP')
            ->andReturn($this->createResponse($currencyJson));

        $uriGenerator->shouldReceive('resolveEndPointPath')
            ->once()
            ->with('/currencies/{id}', ['id' => 'CLP'], [])
            ->andReturn('/currencies/CLP');


        $getAllHelper = new GetByIdHelperStub();

        $getAllHelper->setHttpClient($client);
        $getAllHelper->setUriGenerator($uriGenerator);

        $resourcesList = $getAllHelper->getById('CLP');


        $expectedObject = json_decode($currencyJson);
        $this->assertEquals($expectedObject, $resourcesList);
    }
}

class GetByIdHelperStub
{
    use GetByIdHelper;

    public $path = [
        'get-by-id' => '/currencies/{id}',
    ];

    public function hydrate(ResponseInterface $response)
    {
        $collection = \Mockery::mock('Collection');

        $collection->shouldReceive('first')
            ->once()
            ->withNoArgs()
            ->andReturn(json_decode($response->getBody()->__toString()));

        return $collection;
    }
}