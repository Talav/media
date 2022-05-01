<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Thumbnail\ThumbnailInterface;

trait ThumbnailableProviderTrait
{
    protected ThumbnailInterface $thumbnail;

    public function getThumbnailPath(MediaInterface $media, string $formatName): ?string
    {
        return $this->thumbnail->getThumbPath($this, $media, $this->getFormat($formatName));
    }

    public function setThumbnail(ThumbnailInterface $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }
}
