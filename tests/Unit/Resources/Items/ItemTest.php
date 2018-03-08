<?php

namespace MelTests\Unit\Resources\Items;

use Mel\Http\HttpClient;
use Mel\Http\Responses\Response;
use Mel\Resources\Categories;
use Mel\Resources\Items\Item;
use Mel\Resources\Items\ItemsHelper;
use MelTests\Unit\Fixtures\Responses\ItemHttpResponse as RawItemResponse;
use MelTests\Unit\Fixtures\Responses\ItemHttpResponse;
use MelTests\Unit\Fixtures\Responses\PredictCategoryResponse;
use MelTests\TestCase;
use Mockery;

class ItemTest extends TestCase
{
    /**
     * @var array
     */
    protected $itemDetails;

    /**
     * @var \Mockery\MockInterface|\Mel\Mel
     */
    protected $httpClient;

    /**
     * @var Mockery\MockInterface|\Mel\Resources\Categories
     */
    protected $categoriesHelper;

    /**
     * @var Mockery\MockInterface|\Mel\Resources\Items\Item
     */
    protected $itemsHelper;

    /**
     * @inheritdoc \Mockery\MockInterface|\Mel\Mel
     */
    protected function setUp()
    {
        parent::setUp();

        $this->itemDetails = RawItemResponse::BODY_ARRAY_FORMAT;

        $this->httpClient = Mockery::mock(HttpClient::class);

        $this->categoriesHelper = Mockery::mock(Categories::class);

        $this->itemsHelper = Mockery::mock(ItemsHelper::class);

        $this->melMock->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($this->httpClient);

        $this->melMock->shouldReceive('categories')
            ->once()
            ->withNoArgs()
            ->andReturn($this->categoriesHelper);

        $this->melMock->shouldReceive('items')
            ->once()
            ->withNoArgs()
            ->andReturn($this->itemsHelper);
    }

    public function testGetItemData()
    {
        $productId = $this->itemDetails['id'];
        $finalResponse = new Response(new RawItemResponse());

        $this->httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('GET', '/items/' . $productId)
            ->andReturn($finalResponse);

        $item = new Item($this->melMock, ['id' => 'MLA600190449']);

        $response = $item->get();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($this->itemDetails['id'], $item->id);
        $this->assertEquals($this->itemDetails['title'], $item->title);
        $this->assertEquals($this->itemDetails['currency_id'], $item->currency_id);
    }

    public function testPublishItem()
    {
        $itemDetails = ['title' => 'Product Name', 'description' => 'Product Description'];

        $finalResponse = new Response(new RawItemResponse());

        $this->httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('POST', '/items/', $itemDetails)
            ->andReturn($finalResponse);


        $item = new Item($this->melMock, $itemDetails);

        $response = $item->publish();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($this->itemDetails['id'], $item->id);
        $this->assertEquals($this->itemDetails['title'], $item->title);
        $this->assertEquals($this->itemDetails['currency_id'], $item->currency_id);
    }

    public function testCategorizeItemAutomatically()
    {
        $this->categoriesHelper->shouldReceive('predict')
            ->once()
            ->with($this->itemDetails['title'])
            ->andReturn(new Response(new PredictCategoryResponse()));

        $item = new Item($this->melMock, $this->itemDetails);

        $item->categorize();

        $this->assertEquals(PredictCategoryResponse::BODY_ARRAY_FORMAT['id'], $item->categoryId);
    }

    public function testValidateItemDataUsingClassValidator()
    {
        $item = new Item($this->melMock, $this->itemDetails);

        $this->itemsHelper->shouldReceive('validate')
            ->once()
            ->with($item)
            ->andReturn(true);

        $this->assertTrue($item->isValid());
    }

    public function testShouldUpdateExistentItem()
    {

        $this->httpClient->shouldReceive('sendRequest')
            ->once()
            ->with('PUT', '/items/MLA600190449', ['title' => 'Iphone 6 64gb Space Gray Liberado'])
            ->andReturn(new Response(new ItemHttpResponse()));

        $item = new Item($this->melMock, ['id' => 'MLA600190449']);

        $response = $item->update(['title' => 'Iphone 6 64gb Space Gray Liberado']);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($this->itemDetails['title'], $item->title);
    }
}
