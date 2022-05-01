<?php

namespace Talav\Component\Media\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Model\FileInfo;
use Talav\Component\Media\Model\Media;
use Talav\Component\Media\Model\MediaInterface;

class MediaTest extends TestCase
{
    /**
     * @test
     */
    public function it_calculates_provider_reference_without_extension()
    {
        $media = new Media();
        $media->setFileInfo(new FileInfo(
            10,
            'image/png',
            '',
            'test'
        ));
        self::assertStringNotContainsString('.', $media->getProviderReference());
    }

    /**
     * @test
     */
    public function it_calculates_provider_reference_with_extension()
    {
        $media = $this->createMedia();
        self::assertStringContainsString('.png', $media->getProviderReference());
    }

    /**
     * @test
     */
    public function it_does_not_regenerate_reference_for_existing_reference()
    {
        $media = $this->createMedia();
        $ref = $media->getProviderReference();
        self::assertEquals($ref, $media->getProviderReference());
    }

    private function createMedia(): MediaInterface
    {
        $media = new Media();
        $media->setFileInfo(new FileInfo(
            10,
            'image/png',
            'png',
            'test.png'
        ));

        return $media;
    }
}
