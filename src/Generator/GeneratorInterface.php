<?php

declare(strict_types=1);

namespace Talav\Component\Media\Generator;

use Talav\Component\Media\Model\MediaInterface;

interface GeneratorInterface
{
    public function generatePath(MediaInterface $media): string;
}
