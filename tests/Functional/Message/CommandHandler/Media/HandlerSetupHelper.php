<?php

declare(strict_types=1);

namespace Talav\Component\Media\Tests\Functional\Message\CommandHandler\Media;

use AutoMapperPlus\AutoMapper;
use AutoMapperPlus\Configuration\AutoMapperConfig;
use Doctrine\ORM\EntityManager;
use Faker\Factory as FakerFactory;
use Faker\Generator;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Glide\ServerFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;
use Talav\Component\Media\Cdn\Server;
use Talav\Component\Media\Context\ContextConfig;
use Talav\Component\Media\Generator\UuidGenerator;
use Talav\Component\Media\Manager\MediaManager;
use Talav\Component\Media\Mapper\Dto\CreateMediaMapper;
use Talav\Component\Media\Mapper\Dto\UpdateMediaMapper;
use Talav\Component\Media\Message\Command\Media\CreateMediaCommand;
use Talav\Component\Media\Message\CommandHandler\Media\CreateMediaHandler;
use Talav\Component\Media\Message\CommandHandler\Media\DeleteMediaHandler;
use Talav\Component\Media\Message\CommandHandler\Media\DeleteMediaThumbsHandler;
use Talav\Component\Media\Message\CommandHandler\Media\GenerateMediaThumbsHandler;
use Talav\Component\Media\Message\CommandHandler\Media\UpdateMediaHandler;
use Talav\Component\Media\Message\Dto\Media\CreateMediaDto;
use Talav\Component\Media\Message\Dto\Media\UpdateMediaDto;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\Constraints;
use Talav\Component\Media\Provider\FileProvider;
use Talav\Component\Media\Provider\ImageProvider;
use Talav\Component\Media\Provider\ProviderPool;
use Talav\Component\Media\Tests\Functional\Setup\Entity\MediaEntity;
use Talav\Component\Media\Thumbnail\GlideServer;
use Talav\Component\Media\Thumbnail\ThumbnailInterface;
use Talav\Component\Resource\Factory\Factory;
use Talav\Component\Resource\Manager\ResourceManager;
use Webfactory\Doctrine\ORMTestInfrastructure\ORMInfrastructure;

trait HandlerSetupHelper
{
    protected ORMInfrastructure $infrastructure;

    protected EntityManager $em;

    protected ProviderPool $pool;

    protected Filesystem $fs;

    protected Generator $faker;

    protected AutoMapper $mapper;

    protected ResourceManager $mediaManager;

    protected ValidatorInterface $validator;

    protected ThumbnailInterface $thumbnail;

    protected CreateMediaHandler $createMediaHandler;

    protected UpdateMediaHandler $updateMediaHandler;

    protected DeleteMediaHandler $deleteMediaHandler;

    protected GenerateMediaThumbsHandler $generateMediaThumbsHandler;

    protected DeleteMediaThumbsHandler $deleteMediaThumbsHandler;

    protected function setUpBasics(): void
    {
        $this->fs = new Filesystem(new InMemoryFilesystemAdapter());
        $cdn = new Server(sys_get_temp_dir());
        $generator = new UuidGenerator();
        $this->validator = (new ValidatorBuilder())->getValidator();
        $this->thumbnail = new GlideServer(ServerFactory::create([
            'source' => $this->fs,
            'cache' => $this->fs,
        ]), sys_get_temp_dir());
        $provider1 = new FileProvider('provider1', $this->fs, $cdn, $generator, new Constraints(['txt'], ['mimeTypes' => [
            'text/plain',
        ]], []));
        $provider2 = new FileProvider('provider2', $this->fs, $cdn, $generator, new Constraints(['doc'], [], []));
        $provider3 = new ImageProvider('provider3', $this->fs, $cdn, $generator, new Constraints([], ['mimeTypes' => [
            'image/png',
        ]], []));
        $provider3->addFormat('format1', ['w' => 50, 'h' => 50]);
        $provider3->addFormat('format2', ['w' => 150, 'h' => 150]);
        $provider3->addFormat('format3', ['w' => 250, 'h' => 50]);
        $this->pool = new ProviderPool();
        $this->pool->addContext(new ContextConfig('context1', [$provider1], []));
        $this->pool->addContext(new ContextConfig('context2', [$provider2], []));
        $this->pool->addContext(new ContextConfig('context3', [$provider3], []));
        $this->infrastructure = ORMInfrastructure::createWithDependenciesFor(MediaEntity::class);
        $this->em = $this->infrastructure->getEntityManager();
        $this->faker = FakerFactory::create();

        $config = new AutoMapperConfig();
        $config->getOptions()->createUnregisteredMappings();
        $config->registerMapping(CreateMediaDto::class, MediaInterface::class)
            ->useCustomMapper(new CreateMediaMapper());
        $config->registerMapping(UpdateMediaDto::class, MediaInterface::class)
            ->useCustomMapper(new UpdateMediaMapper());
        $this->mapper = new AutoMapper($config);

        $this->mediaManager = new MediaManager(MediaEntity::class, $this->em, new Factory(MediaEntity::class));

        $messageBus = new MessageBus();

        $this->updateMediaHandler = new UpdateMediaHandler($this->mapper, $this->mediaManager, $this->pool, $this->validator, $messageBus);
        $this->createMediaHandler = new CreateMediaHandler($this->mapper, $this->mediaManager, $this->pool, $this->validator, $messageBus);
        $this->deleteMediaHandler = new DeleteMediaHandler($this->mediaManager, $this->pool, $messageBus);
        $this->generateMediaThumbsHandler = new GenerateMediaThumbsHandler($this->mediaManager, $this->pool, $this->thumbnail);
        $this->deleteMediaThumbsHandler = new DeleteMediaThumbsHandler($this->mediaManager, $this->pool, $this->thumbnail);
    }

    protected function createMedia(string $providerName, string $context, UploadedFile $file): MediaInterface
    {
        $dto = new CreateMediaDto();
        $dto->provider = $providerName;
        $dto->context = $context;
        $dto->name = $this->faker->name;
        $dto->description = $this->faker->text(200);
        $dto->file = $file;
        $command = new CreateMediaCommand($dto);

        return $this->createMediaHandler->__invoke($command);
    }

    protected function createTempTxtFile(string $name, string $content): UploadedFile
    {
        $tempName = tempnam(sys_get_temp_dir(), 'test').'.txt';
        file_put_contents($tempName, $content);

        return new UploadedFile($tempName, $name, null, null, true);
    }

    protected function createTempImageFile(string $name): UploadedFile
    {
        $path = $this->faker->image(sys_get_temp_dir(), 800, 600, 'dogs');

        return new UploadedFile($path, $name, null, null, true);
    }
}
