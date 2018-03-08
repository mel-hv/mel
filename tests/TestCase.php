<?php

namespace MelTests;

use Mel\Mel;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Mockery;

class TestCase extends BaseTestCase
{
    /**
     * @var \Mockery\MockInterface|\Mel\Mel
     */
    protected $melMock;

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
    }

    /**
     * @inheritDoc
     */
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }
}