<?php

declare(strict_types=1);

namespace Talav\MediaBundle\Thumbnail;

use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\MediaProviderInterface;

interface ThumbnailInterface
{
    public function generate(MediaProviderInterface $provider, MediaInterface $media): void;
}