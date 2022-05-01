<?php

declare(strict_types=1);

namespace Talav\Component\Media\Tests\Functional\Message\CommandHandler\Media;

use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Message\Command\Media\CreateMediaCommand;
use Talav\Component\Media\Message\Dto\Media\CreateMediaDto;

class CreateMediaHandlerTest extends TestCase
{
    use HandlerSetupHelper;

    private const FILE_CONTENT = 'File content';
    private const FILE_NAME = 'test_file.txt';

    protected function setUp(): void
    {
        $this->setUpBasics();
    }

    /**
     * @test
     */
    public function it_requires_media_to_have_context()
    {
        self::expectException("\Webmozart\Assert\InvalidArgumentException");
        self::expectExceptionMessage('Media should have context defined');
        $command = new CreateMediaCommand($this->createMediaDto('provider1', null));
        $this->createMediaHandler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_requires_media_to_have_valid_context()
    {
        self::expectException("\Webmozart\Assert\InvalidArgumentException");
        self::expectExceptionMessage('Invalid media context provided');
        $command = new CreateMediaCommand($this->createMediaDto('provider1', 'does_not_exist'));
        $this->createMediaHandler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_requires_media_to_have_provider()
    {
        self::expectException("\Webmozart\Assert\InvalidArgumentException");
        self::expectExceptionMessage('Media should have provider defined');
        $command = new CreateMediaCommand($this->createMediaDto(null));
        $this->createMediaHandler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_requires_media_to_have_valid_provider()
    {
        self::expectException("\Webmozart\Assert\InvalidArgumentException");
        self::expectExceptionMessage('Invalid media provider');
        $command = new CreateMediaCommand($this->createMediaDto('does_not_exist'));
        $this->createMediaHandler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_executes_additional_constraints_such_as_mime_type_validation()
    {
        self::expectException("\Talav\Component\Media\Exception\InvalidMediaException");
        $command = new CreateMediaCommand($this->createMediaDto('provider3'));
        $this->createMediaHandler->__invoke($command);
    }

    /**
     * @test
     */
    public function it_creates_file_with_correct_content()
    {
        $command = new CreateMediaCommand($this->createMediaDto());
        $media = $this->createMediaHandler->__invoke($command);
        self::assertEquals(self::FILE_CONTENT, $this->pool->getProvider($media->getProviderName())->getMediaContent($media));
        self::assertEquals(self::FILE_NAME, $media->getFileInfo()->getName());
    }

    protected function createMediaDto(?string $providerName = 'provider1', ?string $context = 'context1'): CreateMediaDto
    {
        $dto = new CreateMediaDto();
        $dto->provider = $providerName;
        $dto->context = $context;
        $dto->name = $this->faker->name;
        $dto->description = $this->faker->text(200);
        $dto->file = $this->createTempTxtFile(self::FILE_NAME, self::FILE_CONTENT);

        return $dto;
    }
}
