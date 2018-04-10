<?php

namespace MelTests\Unit\Resources\HttpHelpers;

use Mel\Resources\HttpHelpers\GetAllHelper;
use MelTests\TestCase;
use Psr\Http\Message\ResponseInterface;

class GetAllHelperTest extends TestCase
{
    public function testShouldSendRequestToGetListOfTheResources()
    {
        $client = \Mockery::mock('Http\Client\Common\HttpMethodsClient');
        $client->shouldReceive('get')
            ->once()
            ->with('/all')
            ->andReturn(
                $this->createResponse(
                    $this->getJsonFileContent('currencies/currencies-list')
                )
            );


        $getAllHelper = new GetAllHelperStub();

        $getAllHelper->setHttpClient($client);

        $resourcesList = $getAllHelper->getAll();

        $expectedArray = json_decode($this->getJsonFileContent('currencies/currencies-list'), true);
        $this->assertEquals($expectedArray, $resourcesList);
    }
}

class GetAllHelperStub
{
    use GetAllHelper;

    public $path = [
       'get-all' => '/all'
    ];

    public function hydrate(ResponseInterface $response)
    {
        return json_decode($response->getBody()->__toString(), true);
    }
}
