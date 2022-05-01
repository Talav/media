<?php

declare(strict_types=1);

namespace Talav\Component\Media\Message\CommandHandler\Media;

use AutoMapperPlus\AutoMapperInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Talav\Component\Media\Exception\InvalidMediaException;
use Talav\Component\Media\Message\Command\Media\CreateMediaCommand;
use Talav\Component\Media\Message\Command\Media\GenerateMediaThumbsCommand;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\ProviderPool;
use Talav\Component\Resource\Manager\ManagerInterface;
use Webmozart\Assert\Assert;

final class CreateMediaHandler implements MessageHandlerInterface
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

    public function __invoke(CreateMediaCommand $message)
    {
        /** @var MediaInterface $media */
        $media = $this->mediaManager->create();
        $this->mapper->mapToObject($message->getDto(), $media);

        Assert::notNull($media->getContext(), 'Media should have context defined');
        Assert::true($this->providerPool->hasContext($media->getContext()), 'Invalid media context provided');

        Assert::notNull($media->getProviderName(), 'Media should have provider defined');
        Assert::true($this->providerPool->hasProvider($media->getProviderName()), 'Invalid media provider');

        $provider = $this->providerPool->getProvider($media->getProviderName());

        $violations = $this->validator->validate($message->getDto()->file, $provider->getFileFieldConstraints());
        if (0 < $violations->count()) {
            throw new InvalidMediaException((string) $violations);
        }

        $this->mediaManager->update($media, true);
        $provider->setMediaContent($media, $message->getDto()->file);

        $this->messageBus->dispatch(new GenerateMediaThumbsCommand($media));

        return $media;
    }
}
