<?php

declare(strict_types=1);

namespace Talav\Component\Media\Message\CommandHandler\Media;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Talav\Component\Media\Message\Command\Media\DeleteMediaCommand;
use Talav\Component\Media\Message\Command\Media\DeleteMediaThumbsCommand;
use Talav\Component\Media\Provider\ProviderPool;
use Talav\Component\Resource\Manager\ManagerInterface;

final class DeleteMediaHandler implements MessageHandlerInterface
{
    private ManagerInterface $mediaManager;

    private ProviderPool $providerPool;

    private MessageBusInterface $messageBus;

    public function __construct(
        ManagerInterface $mediaManager,
        ProviderPool $providerPool,
        MessageBusInterface $messageBus
    ) {
        $this->mediaManager = $mediaManager;
        $this->providerPool = $providerPool;
        $this->messageBus = $messageBus;
    }

    public function __invoke(DeleteMediaCommand $message): void
    {
        $media = $message->getMedia();
        $previousMedia = clone $media;

        $this->mediaManager->remove($media);
        $this->providerPool->getProvider($previousMedia->getProviderName())->deleteMediaContent($previousMedia);

        $this->messageBus->dispatch(new DeleteMediaThumbsCommand($media));
    }
}
