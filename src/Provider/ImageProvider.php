<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use Talav\Component\Media\Model\MediaInterface;

class ImageProvider extends FileProvider implements ThumbnailableProviderInterface
{
    use ThumbnailableProviderTrait;

    public function getViewHelperProperties(MediaInterface $media, string $formatName, iterable $options = []): array
    {
        if (isset($options['srcset'], $options['picture'])) {
            throw new \LogicException("The 'srcset' and 'picture' options must not be used simultaneously.");
        }

        $params = [
            'alt' => $media->getDescription() ?? $media->getName(),
            'title' => $media->getName(),
            'src' => $this->getThumbnailPublicUrl($media, $formatName),
        ];
        $params['width'] = $media->getThumbsInfo()[$formatName]['width'];
        $params['height'] = $media->getThumbsInfo()[$formatName]['height'];

        // add logic to process $options['picture']

        return array_merge($params, $options);
    }

    public function getThumbnailPublicUrl(MediaInterface $media, string $formatName): ?string
    {
        return $this->cdn->getPath($this->getThumbnailPath($media, $formatName));
    }
}
