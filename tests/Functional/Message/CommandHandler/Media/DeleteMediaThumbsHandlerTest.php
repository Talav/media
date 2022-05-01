<?php

declare(strict_types=1);

namespace Talav\Component\Media\Tests\Functional\Message\CommandHandler\Media;

use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Message\Command\Media\DeleteMediaThumbsCommand;
use Talav\Component\Media\Message\Command\Media\GenerateMediaThumbsCommand;
use Talav\Component\Media\Model\MediaInterface;

class DeleteMediaThumbsHandlerTest extends TestCase
{
    use HandlerSetupHelper;

    protected function setUp(): void
    {
        $this->setUpBasics();
    }

    /**
     * @test
     */
    public function it_skips_media_if_provider_does_not_support_thumbnails()
    {
        $media = $this->createTxtMedia();
        $command = new GenerateMediaThumbsCommand($media);
        $this->generateMediaThumbsHandler->__invoke($command);
        $media = $this->mediaManager->reload($media);
        self::assertCount(0, $media->getThumbsInfo());
    }

    /**
     * @test
     */
    public function it_deletes_all_thumbnails()
    {
        $media = $this->createImageMedia();
        $command = new GenerateMediaThumbsCommand($media);
        $this->generateMediaThumbsHandler->__invoke($command);

        $command = new DeleteMediaThumbsCommand($media);
        $this->deleteMediaThumbsHandler->__invoke($command);

        $media = $this->mediaManager->reload($media);
        self::assertCount(0, $media->getThumbsInfo());

        $provider = $this->pool->getProvider($media->getProviderName());
        foreach ($provider->getFormats() as $format) {
            $this->assertFalse($this->thumbnail->isThumbExists($provider, $media, $format));
        }
    }

    protected function createTxtMedia(): MediaInterface
    {
        return $this->createMedia(
            'provider1',
            'context1',
            $this->createTempTxtFile('name.txt', 'content')
        );
    }

    protected function createImageMedia(): MediaInterface
    {
        return $this->createMedia(
            'provider3',
            'context1',
            $this->createTempImageFile('test.jpeg')
        );
    }
}
