<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Thumbnail\ThumbnailInterface;

interface ThumbnailableProviderInterface
{
    public function getThumbnailPublicUrl(MediaInterface $media, string $formatName): ?string;

    public function setThumbnail(ThumbnailInterface $thumbnail): void;
}
