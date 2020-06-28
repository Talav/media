<?php

declare(strict_types=1);

namespace Talav\MediaBundle\Thumbnail;

use League\Glide\Server;
use Talav\Component\Media\Exception\FilesystemException;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\MediaProviderInterface;
use Talav\Component\Media\Provider\ProviderPool;
use Talav\Component\Resource\Error\ErrorHandlerTrait;

final class GlideServer implements ThumbnailInterface
{
    use ErrorHandlerTrait;

    /**
     * @var Server
     */
    protected $server;

    /**
     * @var ProviderPool
     */
    protected $pool;

    public function generate(MediaProviderInterface $provider, MediaInterface $media): void
    {
        $tmp = $this->getTemporaryFile();
        $imageData = $this->getImageData($media);
        $this->disableErrorHandler();
        if (file_put_contents($tmp, $imageData) === false) {
            $this->restoreErrorHandler();
            throw new FilesystemException('Unable to write temporary file');
        }
        $this->restoreErrorHandler();

        try {
            $filesystem = $this->pool->getProvider($media)->getFilesystem();
            $this->server->setSource($filesystem);
            $this->server->setCache($filesystem);
//            foreach ($this->pool->getContext($media)->) {
//
//            }
//            $this->server->getApi()->run($tmp, ['p' => ]);
//            $this->server->getCache()->put($filename, $this->doGenerateImage($media, $tmp, $parameterBag));
        } catch (\Exception $e) {
            throw new FilesystemException('Could not generate image', 0, $e);
        } finally {
            if (file_exists($tmp)) {
                if (!@unlink($tmp)) {
                    throw new FilesystemException('Unable to clean up temporary file');
                }
            }
        }

//        $referenceFile = $provider->getFilesystemReference($media);
//
//        if (!$referenceFile->exists()) {
//            return;
//        }
//
//        foreach ($provider->getFormats() as $format => $settings) {
//            if (substr($format, 0, \strlen($media->getContext())) === $media->getContext() ||
//                MediaProviderInterface::FORMAT_ADMIN === $format) {
//                $resizer = (isset($settings['resizer']) && ($settings['resizer'])) ?
//                    $this->getResizer($settings['resizer']) :
//                    $provider->getResizer();
//                $resizer->resize(
//                    $media,
//                    $referenceFile,
//                    $provider->getFilesystem()->get($provider->generatePrivateUrl($media, $format), true),
//                    $this->getExtension($media),
//                    $settings
//                );
//            }
//        }
    }

    protected function getImageData(MediaInterface $media): string
    {
        if ($this->server->getSource()->has($media->getImage())) {
            return $this->server->getSource()->read($media->getImage());
        }
        throw new FilesystemException('File not found');
    }

    protected function getTemporaryFile(): string
    {
        if (empty($this->tmpPath)) {
            $this->tmpPath = sys_get_temp_dir();
        }
        if (empty($this->tmpPrefix)) {
            $this->tmpPrefix = 'media';
        }

        $this->disableErrorHandler();
        $tempFile = tempnam($this->tmpPath, $this->tmpPrefix);
        $this->restoreErrorHandler();

        return $tempFile;
    }

    protected function getProvider(MediaInterface $media): MediaProviderInterface
    {
        return $this->pool->getProvider($media->getProviderName());
    }

    protected function getContext(MediaInterface $media): array
    {
        return $this->pool->getContext($media->getContext());
    }
}