<?php

declare(strict_types=1);

namespace Sonata\MediaBundle\Generator;

namespace Talav\Component\Media\Generator;

use Talav\Component\Media\Model\MediaInterface;

class UuidGenerator implements GeneratorInterface
{
    public function generatePath(MediaInterface $media): string
    {
        $id = (string) $media->getId();

        return sprintf('%s/%04s/%02s', $media->getContext(), substr($id, 0, 4), substr($id, 4, 2));
    }
}
