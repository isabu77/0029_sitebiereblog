<?php

namespace Tests\Core\Controller\Session;

use PHPUnit\Framework\TestCase;
use \Core\Controller\Session\FlashService;
use \Core\Controller\Session\ArraySession;

class FlashServiceTest extends TestCase
{
    public function testHasMessageSuccessWithoutMessage(): void
    {
        $flash = new FlashService(new ArraySession(), true);

        $this->assertEquals(false, $flash->hasMessage("success") );

    }

    public function testGetMessagesSuccessWithoutMessage(): void
    {
        $flash = new FlashService(new ArraySession(), true);

        $this->assertEquals([], $flash->getMessages("success") );

    }

    public function testGetMessagesSuccessWithMessage(): void
    {
        $flash = new FlashService(new ArraySession(), true);
        $flash->addSuccess("ça marche");
        $this->assertEquals(["ça marche"], $flash->getMessages("success") );

    }

    public function testHasMessageWithoutMessage(): void
    {
        $flash = new FlashService(new ArraySession(), true);
        $this->assertEquals(false, $flash->hasMessage("success") );

    }

    public function testHasMessageWithMessage(): void
    {
        $flash = new FlashService(new ArraySession(), true);
        $flash->addSuccess("ça marche");
        $this->assertTrue($flash->hasMessage("success") );

    }
}