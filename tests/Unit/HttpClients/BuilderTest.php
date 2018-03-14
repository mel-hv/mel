<?php

namespace MelTests\Unit\HttpClients;

use Mel\HttpClient\Builder;
use Http\Client\Common\Plugin;
use Http\Message\RequestFactory;
use Http\Client\HttpClient;
use Mel\Mel;
use Mockery;
use MelTests\TestCase;

class BuilderTest extends TestCase
{
    /**
     * @var Builder
     */
    protected $builderClient;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->builderClient = new Builder(
            $this->mockClient,
            Mockery::mock((RequestFactory::class))
        );
    }

    public function testHttpClientShouldBeAnHttpMethodsClient()
    {
        $this->assertInstanceOf(HttpClient::class, $this->builderClient->getHttpClient());
    }

    public function testAddPluginShouldCreateNewHttpClientInstance()
    {
        $client = $this->builderClient->getHttpClient();

        $this->builderClient->addPlugin(Mockery::mock((Plugin::class)));

        $this->assertNotSame($client, $this->builderClient->getHttpClient());
        $this->assertAttributeNotEmpty('plugins', $this->builderClient);
    }

    public function testRemovePluginShouldCreateNewHttpClientInstance()
    {
        $this->builderClient->addPlugin(Mockery::mock((Plugin::class)));

        $client = $this->builderClient->getHttpClient();

        $this->builderClient->removePlugin(Plugin::class);

        $this->assertNotSame($client, $this->builderClient->getHttpClient());
        $this->assertAttributeEmpty('plugins', $this->builderClient);
    }

    public function testCreateBuilderInstanceUsingBasicPluginsToUse()
    {
        $builder = Builder::create($this->mockClient);

        // Test send request
        $builder->getHttpClient()
            ->sendRequest(
              $this->createRequest('POST', '/')
            );

        $requests = $this->mockClient->getRequests();



        $this->assertInstanceOf(Builder::class, $builder);
        $this->assertAttributeCount(1, 'plugins', $builder);

        // Assert Default Headers
        $this->assertEquals('MEL - '. Mel::VERSION, $requests[0]->getHeaderLine('User-Agent'));
        $this->assertEquals('application/json', $requests[0]->getHeaderLine('Content-Type'));
    }
}
