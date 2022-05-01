<?php

declare(strict_types=1);

namespace Talav\Component\Media\Message\CommandHandler\Media;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Talav\Component\Media\Message\Command\Media\DeleteMediaThumbsCommand;
use Talav\Component\Media\Provider\ProviderPool;
use Talav\Component\Media\Provider\ThumbnailableProviderInterface;
use Talav\Component\Media\Thumbnail\ThumbnailInterface;
use Talav\Component\Resource\Manager\ManagerInterface;

final class DeleteMediaThumbsHandler implements MessageHandlerInterface
{
    private ManagerInterface $mediaManager;

    private ProviderPool $providerPool;

    private ThumbnailInterface $thumbnail;

    public function __construct(
        ManagerInterface $mediaManager,
        ProviderPool $providerPool,
        ThumbnailInterface $thumbnail
    ) {
        $this->mediaManager = $mediaManager;
        $this->providerPool = $providerPool;
        $this->thumbnail = $thumbnail;
    }

    public function __invoke(DeleteMediaThumbsCommand $message): void
    {
        $media = $message->getMedia();
        $provider = $this->providerPool->getProvider($media->getProviderName());
        if (!($provider instanceof ThumbnailableProviderInterface)) {
            return;
        }
        $this->thumbnail->delete($provider, $media);
        $media->setThumbsInfo([]);
        $this->mediaManager->update($media, true);
    }
}
