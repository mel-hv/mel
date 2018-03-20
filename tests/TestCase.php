<?php

namespace MelTests;

use Http\Mock\Client;
use Mel\Mel;
use Mel\MeLiApp;
use Mel\Country;
use Mockery;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Discovery\HttpClientDiscovery;

class TestCase extends BaseTestCase
{
    /**
     * @var \Mockery\MockInterface|\Mel\Mel
     */
    protected $melMock;

    /**
     * @var \Http\Mock\Client
     */
    protected $mockClient;

    /**
     * @var string Mercado Libre uri of the api
     */
    protected $apiUri = 'https://api.mercadolibre.com/';

    /**
     * @var string It is the APP ID of the application you created
     * @see http://developers.mercadolibre.com/server-side/
     */
    protected $appId = '4242424242424242';

    /**
     * @var string The Secret Key generated for your application when created
     * @see http://developers.mercadolibre.com/server-side/
     */
    protected $secretKey = 'ThisIsASimpleStringCreatedToTests';

    /**
     * @var string The redirect URI configured for your application or one of the allowed domains
     */
    protected $redirectUri = 'https://redirect-uri.com/auth';

    /**
     * @var string An access key to private resources valid for 6 hours
     * @see http://developers.mercadolibre.com/server-side/
     */
    protected $accessToken = 'APP_USR-6092-3246532-cb45c82853f6e620bb0deda096b128d3-8035443';

    /**
     * @var string Used to be later exchanged for a new access_token upon expiration
     * @see http://developers.mercadolibre.com/server-side/
     */
    protected $refreshToken = 'TG-5005b6b3e4b07e60756a3353';

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->melMock = Mockery::mock(Mel::class);

        $this->mockClient = new Client();

        HttpClientDiscovery::prependStrategy(MockClientStrategy::class);
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Return Mel instance
     *
     * @return Mel
     * @throws \Exception
     * @throws \Mel\Exceptions\MelException
     */
    protected function getMel()
    {
        $meLiApp = new MeLiApp($this->appId, $this->secretKey, $this->redirectUri);

        $mel = new Mel($meLiApp, new Country(Country::BRASIL));

        return $mel;
    }

    /**
     * Create PsrResponse instance
     *
     * @param string|array $content
     * @param int          $status
     * @param null         $reasonPhrase
     * @param array        $headers
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function createResponse($content, $status = 202, $reasonPhrase = null, $headers = [])
    {
        $body = '';

        if (is_string($content)) {
            $body = $content;
        }

        if (is_array($content)) {
            $body = json_encode($content);
        }

        $response = MessageFactoryDiscovery::find()->createResponse($status, $reasonPhrase, $headers, $body);

        return $response;
    }

    /**
     * Create PsrRequest instance
     *
     * @param string $method
     * @param        $uri
     * @param array  $headers
     * @param null   $body
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function createRequest($method, $uri, $headers = [], $body = null)
    {
        return MessageFactoryDiscovery::find()->createRequest($method, $uri, $headers, $body);
    }

    /**
     * Get json content of the file that is in directory fixtures/json
     *
     * @param $filePath
     *
     * @return bool|string
     */
    protected function getJsonFileContent($filePath)
    {
        $filePath = 'tests/Unit/Fixtures/json/' . trim($filePath, '/');

        return file_get_contents($filePath);
    }
}