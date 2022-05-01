<?php

declare(strict_types=1);

namespace Talav\Component\Media\Thumbnail;

use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\MediaProviderInterface;

interface ThumbnailInterface
{
    public function generate(MediaProviderInterface $provider, MediaInterface $media): iterable;

    public function delete(MediaProviderInterface $provider, MediaInterface $media): void;

    public function isThumbExists(MediaProviderInterface $provider, MediaInterface $media, array $options): bool;
}
