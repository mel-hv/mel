<?php

namespace MelTests\Unit;

use Mel\MeLiApp;
use MelTests\TestCase;

class MeLiAppTest extends TestCase
{
    public function testShouldSaveMercadoLibreAppSettings()
    {
        $meLiApp = new MeLiApp($this->appId, $this->secretKey, $this->redirectUri);

        $this->assertEquals($this->appId, $meLiApp->clientId());
        $this->assertEquals($this->secretKey, $meLiApp->secretKey());
        $this->assertEquals($this->redirectUri, $meLiApp->redirectUri());
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
