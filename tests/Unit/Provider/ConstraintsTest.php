<?php

namespace Talav\Media\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Provider\Constraints;

class ConstraintsTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_2_constraints_when_no_additional_image_parameters(): void
    {
        $constraints = (new Constraints(['txt']))->getFieldConstraints();
        self::assertCount(2, $constraints);
        self::assertInstanceOf('Symfony\Component\Validator\Constraints\Callback', $constraints[0]);
        self::assertInstanceOf('Symfony\Component\Validator\Constraints\File', $constraints[1]);
    }

    /**
     * @test
     */
    public function it_returns_image_constraints_when_additional_image_parameters(): void
    {
        $constraints = (new Constraints(['txt'], [], ['maxHeight' => 300]))->getFieldConstraints();
        self::assertCount(2, $constraints);
        self::assertInstanceOf('Symfony\Component\Validator\Constraints\Callback', $constraints[0]);
        self::assertInstanceOf('Symfony\Component\Validator\Constraints\Image', $constraints[1]);
    }
}
