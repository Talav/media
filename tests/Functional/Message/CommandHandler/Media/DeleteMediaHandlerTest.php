<?php

declare(strict_types=1);

namespace Talav\Component\Media\Tests\Functional\Message\CommandHandler\Media;

use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Message\Command\Media\DeleteMediaCommand;
use Talav\Component\Media\Model\MediaInterface;

class DeleteMediaHandlerTest extends TestCase
{
    use HandlerSetupHelper;

    protected function setUp(): void
    {
        $this->setUpBasics();
    }

    /**
     * @test
     */
    public function it_deletes_media_and_files()
    {
        $media = $this->createInitialMedia();
        $id = $media->getId();
        $provider = $this->pool->getProvider($media->getProviderName());
        $command = new DeleteMediaCommand($media);
        $this->deleteMediaHandler->__invoke($command);
        self::assertNull($this->mediaManager->getRepository()->find($id));
        self::assertFalse($provider->getFilesystem()->fileExists($provider->getFilesystemReference($media)));
    }

    protected function createInitialMedia(): MediaInterface
    {
        return $this->createMedia(
            'provider1',
            'context1',
            $this->createTempTxtFile('name.txt', 'content')
        );
    }
}
