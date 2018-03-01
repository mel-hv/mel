<?php

namespace MelTests\Unit\Auth\Storage;

use Mel\Auth\Storage\SessionStorage;
use MelTests\TestCase;

class SessionStorageTest extends TestCase
{
    public function testShouldSetSaveItemInSession()
    {
        $sessionManager = new SessionStorage();

        $sessionManager->setPrefix('mel.test.');

        $sessionManager->set('class', 'SessionStorage');

        $this->assertNull($sessionManager->get('fake-item'));
        $this->assertTrue($sessionManager->has('class'));
        $this->assertEquals('SessionStorage', $sessionManager->get('class'));
        $this->assertAttributeContains('SessionStorage', 'session', $sessionManager);
    }

    public function testShouldOverwriteOldItemWithNewValue()
    {
        $sessionManager = new SessionStorage();

        $sessionManager->setPrefix('mel.test.');

        $sessionManager->set('item', 'old-value');

        $sessionManager->set('item', 'new-value');

        $this->assertTrue($sessionManager->has('item'));
        $this->assertNotEquals('old-value', $sessionManager->get('item'));
        $this->assertEquals('new-value', $sessionManager->get('item'));
    }
}
