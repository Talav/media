<?php

declare(strict_types=1);

namespace Talav\Component\Media\Tests\Functional\Message\CommandHandler\Media;

use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Message\Command\Media\UpdateMediaCommand;
use Talav\Component\Media\Message\Dto\Media\UpdateMediaDto;
use Talav\Component\Media\Model\MediaInterface;

class UpdateMediaHandlerTest extends TestCase
{
    use HandlerSetupHelper;

    private const FILE_CONTENT1 = 'File content';
    private const FILE_NAME1 = 'test_file1.txt';

    private const FILE_CONTENT2 = 'File content updated';
    private const FILE_NAME2 = 'test_file2.txt';

    protected function setUp(): void
    {
        $this->setUpBasics();
    }

    /**
     * @test
     */
    public function it_updates_name_and_description_when_file_not_provided()
    {
        $media = $this->createInitialMedia();
        $previousMedia = clone $media;
        $command = new UpdateMediaCommand($media, $this->updateMediaDto());
        $this->updateMediaHandler->__invoke($command);
        self::assertNotEquals($media->getName(), $previousMedia->getName());
        self::assertNotEquals($media->getDescription(), $previousMedia->getDescription());
        self::assertEquals($media->getProviderReference(), $previousMedia->getProviderReference());
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_mime_type_incorrect()
    {
        self::expectException("\Talav\Component\Media\Exception\InvalidMediaException");
        $media = $this->createInitialMedia();
        $updateDto = $this->updateMediaDto();
        $updateDto->file = $this->createTempImageFile('test.png');
        $command = new UpdateMediaCommand($media, $updateDto);
        $this->updateMediaHandler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_replaces_media_file_and_deletes_previous()
    {
        $media = $this->createInitialMedia();
        $previousMedia = clone $media;
        $provider = $this->pool->getProvider($media->getProviderName());
        $updateDto = $this->updateMediaDto();
        $updateDto->file = $this->createTempTxtFile(self::FILE_NAME2, self::FILE_CONTENT2);
        $command = new UpdateMediaCommand($media, $updateDto);
        $this->updateMediaHandler->__invoke($command);

        self::assertNotEquals($previousMedia->getName(), $media->getName());
        self::assertNotEquals($previousMedia->getFileInfo()->getSize(), $media->getFileInfo()->getSize());
        self::assertNotEquals($previousMedia->getProviderReference(), $media->getProviderReference());
        self::assertEquals(self::FILE_CONTENT2, $provider->getMediaContent($media));
        self::assertFalse($provider->getFilesystem()->fileExists($provider->getFilesystemReference($previousMedia)));
    }

    protected function updateMediaDto(): UpdateMediaDto
    {
        $dto = new UpdateMediaDto();
        $dto->name = $this->faker->name;
        $dto->description = $this->faker->text(200);

        return $dto;
    }

    protected function createInitialMedia(): MediaInterface
    {
        return $this->createMedia(
            'provider1',
            'context1',
            $this->createTempTxtFile(self::FILE_NAME1, self::FILE_CONTENT1)
        );
    }
}
