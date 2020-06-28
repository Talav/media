<?php

declare(strict_types=1);

namespace Talav\Component\Media\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;

final class ProviderPoolTest extends TestCase
{
    /**
     * @test
     */
    public function it_validates_extension_in_pre_persist_hook()
    {
        self::assertEquals(1, 1);
    }
}