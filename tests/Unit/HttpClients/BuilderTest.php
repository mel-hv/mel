<?php

namespace MelTests\Unit\HttpClients;

use Mel\HttpClient\Builder;
use Http\Client\Common\Plugin;
use Http\Message\RequestFactory;
use Http\Client\HttpClient;
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
}
