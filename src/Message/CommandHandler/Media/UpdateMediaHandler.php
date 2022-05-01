<?php

declare(strict_types=1);

namespace Talav\Component\Media\Message\CommandHandler\Media;

use AutoMapperPlus\AutoMapperInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Talav\Component\Media\Exception\InvalidMediaException;
use Talav\Component\Media\Message\Command\Media\GenerateMediaThumbsCommand;
use Talav\Component\Media\Message\Command\Media\UpdateMediaCommand;
use Talav\Component\Media\Provider\ProviderPool;
use Talav\Component\Resource\Manager\ManagerInterface;

final class UpdateMediaHandler implements MessageHandlerInterface
{
    private AutoMapperInterface $mapper;

    private ManagerInterface $mediaManager;

    private ProviderPool $providerPool;

    private ValidatorInterface $validator;

    private MessageBusInterface $messageBus;

    public function __construct(
        AutoMapperInterface $mapper,
        ManagerInterface $mediaManager,
        ProviderPool $providerPool,
        ValidatorInterface $validator,
        MessageBusInterface $messageBus
    ) {
        $this->mapper = $mapper;
        $this->mediaManager = $mediaManager;
        $this->providerPool = $providerPool;
        $this->validator = $validator;
        $this->messageBus = $messageBus;
    }

    public function __invoke(UpdateMediaCommand $message)
    {
        $media = $message->getMedia();
        $dto = $message->getDto();
        $oldMedia = clone $media;

        $this->mapper->mapToObject($dto, $media);

        // early exit if file is not updated
        if (is_null($dto->file)) {
            $this->mediaManager->update($media, true);

            return $media;
        }

        $provider = $this->providerPool->getProvider($media->getProviderName());
        $violations = $this->validator->validate($dto->file, $provider->getFileFieldConstraints());
        if (0 < $violations->count()) {
            throw new InvalidMediaException((string) $violations);
        }
        // delete previous file
        $provider->deleteMediaContent($oldMedia);
        // copy new file
        $provider->setMediaContent($media, $dto->file);
        $media->setThumbsInfo([]);

        $this->mediaManager->update($media, true);
        $this->messageBus->dispatch(new GenerateMediaThumbsCommand($media));

        return $media;
    }
}
