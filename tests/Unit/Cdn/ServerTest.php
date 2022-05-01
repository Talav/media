<?php

namespace Talav\Media\Tests\Unit\Cdn;

use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Cdn\Server;

class ServerTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_path(): void
    {
        $server = new Server('https://test.com/');
        self::assertEquals('https://test.com/test.txt', $server->getPath('/test.txt'));
    }
}
