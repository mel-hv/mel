<?php

namespace MelTests\Unit\Resources;

use DusanKasan\Knapsack\Collection;
use Http\Discovery\UriFactoryDiscovery;
use Mel\Resources\AbstractResource;
use Psr\Http\Message\UriInterface;
use MelTests\TestCase;

class AbstractResourceTest extends TestCase
{
    /**
     * @var BasicStubResource
     */
    protected $abstractResource;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();
        $mel = $this->getMel($this->mockClient);

        $this->abstractResource = new BasicStubResource($mel);
    }


    public function testShouldSaveAndReturnResourceAttributesDynamically()
    {
        $this->abstractResource->id = 42;

        $this->assertEquals(42, $this->abstractResource->id);
        $this->assertTrue(isset($this->abstractResource->id));

        unset($this->abstractResource->id);
        $this->assertFalse(isset($this->abstractResource->id));

    }

    public function testShouldSaveAndReturnAttributesUsingArrayAccess()
    {
        $this->abstractResource['id'] = 42;

        $this->assertEquals(42, $this->abstractResource['id']);
        $this->assertTrue(isset($this->abstractResource['id']));


        unset($this->abstractResource['id']);
        $this->assertFalse(isset($this->abstractResource['id']));
    }

    public function testSaveAndReturnAttributesWithCustomFormats()
    {
        $this->abstractResource->permalink = $this->apiUri;

        $this->assertInstanceOf(UriInterface::class, $this->abstractResource->permalink);
        $this->assertEquals($this->apiUri, $this->abstractResource->permalink_string);
    }

    public function testShouldCreateACollectionUsingAHttpResponseWithMultiplesItems()
    {
        $response = $this->createResponse(
            $this->getJsonFileContent('currencies/currencies-list')
        );

        $currenciesArray = json_decode($this->getJsonFileContent('currencies/currencies-list'), true);

        $collection = $this->abstractResource->hydrate($response);

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

        $collection = $this->abstractResource->hydrate($response);

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