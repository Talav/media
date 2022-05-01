<?php

declare(strict_types=1);

namespace Talav\Component\Media\Message\Command\Media;

use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Resource\Model\AsyncDomainMessage;
use Talav\Component\Resource\Model\DomainEventInterface;

final class GenerateMediaThumbsCommand implements DomainEventInterface, AsyncDomainMessage
{
    private MediaInterface $media;

    public function __construct(MediaInterface $media)
    {
        $this->media = $media;
    }

    public function getMedia(): MediaInterface
    {
        return $this->media;
    }
}
