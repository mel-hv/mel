<?php

namespace MelTests\Unit\Resources\Items;

use Mel\Http\HttpClient;
use Mel\Http\Responses\Response;
use Mel\Resources\Categories;
use Mel\Resources\Items\Item;
use MelTests\Unit\Fixtures\Responses\ItemHttpResponse as RawItemResponse;
use MelTests\TestCase;
use MelTests\Unit\Fixtures\Responses\PredictCategoryResponse;
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
     * @inheritdoc \Mockery\MockInterface|\Mel\Mel
     */
    protected function setUp()
    {
        parent::setUp();

        $this->itemDetails = RawItemResponse::BODY_ARRAY_FORMAT;

        $this->httpClient = Mockery::mock(HttpClient::class);

        $this->categoriesHelper = Mockery::mock(Categories::class);

        $this->melMock->shouldReceive('httpClient')
            ->once()
            ->withNoArgs()
            ->andReturn($this->httpClient);

        $this->melMock->shouldReceive('categories')
            ->once()
            ->withNoArgs()
            ->andReturn($this->categoriesHelper);
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
        $itemDetails = ['title' => 'Product Name', 'description' => 'Product Description'];


        $this->categoriesHelper->shouldReceive('predict')
            ->once()
            ->with('Product Name')
            ->andReturn(new Response(new PredictCategoryResponse()));

        $item = new Item($this->melMock, $itemDetails);

        $item->categorize();

        $this->assertEquals(PredictCategoryResponse::BODY_ARRAY_FORMAT['id'], $item->categoryId);
    }
}
