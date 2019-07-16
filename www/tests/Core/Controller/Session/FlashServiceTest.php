<?php

namespace Tests\Core\Controller\Session;

use PHPUnit\Framework\TestCase;
use \Core\Controller\Session\FlashService;

class FlashServiceTest extends TestCase
{
    public function testHasMessageSuccessWithoutMessage(): void
    {
        $flash = new FlashService(true);

        $this->assertEquals(false, $flash->hasMessage("success") );

    }

    public function testGetMessagesSuccessWithoutMessage(): void
    {
        $flash = new FlashService(true);

        $this->assertEquals([], $flash->getMessages("success") );

    }

    public function testGetMessagesSuccessWithMessage(): void
    {
        $flash = new FlashService(true);
        $flash->addSuccess("ça marche");
        $this->assertEquals(["ça marche"], $flash->getMessages("success") );

    }
}