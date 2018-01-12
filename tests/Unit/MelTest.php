<?php

namespace MelTests\Unit;

use Mel\Mel;
use Mel\MeLiApp;
use PHPUnit\Framework\TestCase;
use Mockery;

class MelTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShouldReturnMeLiAppInstance()
    {
        $meLiApp = Mockery::mock(MeLiApp::class);

        $mel = new Mel($meLiApp);

        $this->assertInstanceOf(MeLiApp::class, $mel->meLiApp());
    }

    public function testShouldConfigureAnonymousModeToClient()
    {
        $mel = new Mel();

        $this->assertInstanceOf(MeLiApp::class, $mel->meLiApp());
        $this->assertTrue($mel->meLiApp()->isAnonymousClient());
    }
}
