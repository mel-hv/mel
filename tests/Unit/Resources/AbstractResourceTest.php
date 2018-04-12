<?php

namespace MelTests\Unit\Resources;

use DusanKasan\Knapsack\Collection;
use Http\Discovery\UriFactoryDiscovery;
use Mel\Resources\AbstractResource;
use Psr\Http\Message\UriInterface;
use MelTests\TestCase;

class AbstractResourceTest extends TestCase
{
    public function testShouldSaveAndReturnResourceAttributesDynamically()
    {
        $resource = new BasicStubResource($this->melMock);

        $resource->id = 42;

        $this->assertEquals(42, $resource->id);
        $this->assertTrue(isset($resource->id));

        unset($resource->id);
        $this->assertFalse(isset($resource->id));

    }

    public function testShouldSaveAndReturnAttributesUsingArrayAccess()
    {
        $resource = new BasicStubResource($this->melMock);

        $resource['id'] = 42;

        $this->assertEquals(42, $resource['id']);
        $this->assertTrue(isset($resource['id']));


        unset($resource['id']);
        $this->assertFalse(isset($resource['id']));
    }

    public function testSaveAndReturnAttributesWithCustomFormats()
    {
        $resource = new BasicStubResource($this->melMock);

        $resource->permalink = $this->apiUri;

        $this->assertInstanceOf(UriInterface::class, $resource->permalink);
        $this->assertEquals($this->apiUri, $resource->permalink_string);
    }

    public function testShouldCreateACollectionUsingAHttpResponseWithMultiplesItems()
    {
        $response = $this->createResponse(
            $this->getJsonFileContent('currencies/currencies-list')
        );

        $currenciesArray = json_decode($this->getJsonFileContent('currencies/currencies-list'), true);

        $resource = new BasicStubResource($this->melMock);
        $collection = $resource->hydrate($response);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(count($currenciesArray), $collection->size());
        $this->assertEquals($currenciesArray[0]['id'], $collection->first()->id);
    }

    public function testShouldCreateACollectionUsingHttpResponseWithSingleItem()
    {
        $response = $this->createResponse(
            $this->getJsonFileContent('currencies/single-currency')
        );

        $currencyArray = json_decode($this->getJsonFileContent('currencies/single-currency'), true);

        $resource = new BasicStubResource($this->melMock);
        $collection = $resource->hydrate($response);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertEquals(1, $collection->size());
        $this->assertEquals($currencyArray['id'], $collection->first()->id);
    }
}

class BasicStubResource extends AbstractResource
{
    public function setPermalinkAttribute($value)
    {
        $value = $value instanceof UriInterface ? $value : UriFactoryDiscovery::find()->createUri($value);

        $this->attributes['permalink'] = $value;
    }

    public function getPermalinkStringAttribute()
    {
        return $this->permalink->__toString();
    }
}