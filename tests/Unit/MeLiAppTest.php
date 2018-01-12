<?php

namespace MelTests\Unit;

use Mel\MeLiApp;
use PHPUnit\Framework\TestCase;

class MeLiAppTest extends TestCase
{
    public function testShouldSaveMercadoLibreAppSettings()
    {
        $appId = 'app-id';
        $secretKey = 'secret-key';
        $redirectUri = 'http://redirect-uri.com/callback.php';

        $meLiApp = new MeLiApp($appId, $secretKey, $redirectUri);

        $this->assertEquals($appId, $meLiApp->clientId());
        $this->assertEquals($secretKey, $meLiApp->secretKey());
        $this->assertEquals($redirectUri, $meLiApp->redirectUri());
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage The Mel\MeLiApp:appId and Mel\MeLiApp:secretKey should not be empty
     */
    public function testThrowExceptionIfAppIdOrSecretKeyIsEmpty()
    {
        new MeLiApp('');
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Use valid uri in Mel\MeLiApp:redirectUri
     */
    public function testThrowExceptionIfRedirectUriIsInvalid() {
        new MeLiApp('123132', '1234234');
    }

    public function testShouldConfigureAnonymousMode()
    {
        $meLiApp = new MeLiApp(MeLiApp::ANONYMOUS_MODE);

        $this->assertTrue($meLiApp->isAnonymousClient());
    }
}
