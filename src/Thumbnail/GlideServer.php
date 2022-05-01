<?php

declare(strict_types=1);

namespace Talav\Component\Media\Thumbnail;

use League\Glide\Server;
use Talav\Component\Media\Exception\FilesystemException;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Media\Provider\MediaProviderInterface;

final class GlideServer implements ThumbnailInterface
{
    protected Server $server;

    protected string $tempDir;

    public function __construct(Server $server, string $tempDir)
    {
        $this->server = $server;
        $this->server->setCacheWithFileExtensions(true);
        $this->tempDir = rtrim($tempDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    }

    public function generate(MediaProviderInterface $provider, MediaInterface $media): iterable
    {
        $server = $this->configServer($provider, $media);
        $sizes = [];
        try {
            foreach ($provider->getFormats() as $formatName => $options) {
                $options = $this->enforceExtension($options, $media);
                $path = $server->makeImage($provider->getFilesystemReference($media), $options);
                $sizes[$formatName] = $this->extractSizes($path);
            }
        } catch (\Exception $e) {
            throw new FilesystemException('Could not generate image', 0, $e);
        }

        return $sizes;
    }

    public function delete(MediaProviderInterface $provider, MediaInterface $media): void
    {
        $server = $this->configServer($provider, $media);

        try {
            foreach ($provider->getFormats() as $options) {
                $options = $this->enforceExtension($options, $media);
                $reference = $provider->getFilesystemReference($media);
                if ($server->cacheFileExists($reference, $options)) {
                    $server->getCache()->delete($server->getCachePath($reference, $options));
                }
            }
        } catch (\Exception $e) {
            throw new FilesystemException('Could not delete image', 0, $e);
        }
    }

    public function isThumbExists(MediaProviderInterface $provider, MediaInterface $media, array $options): bool
    {
        $options = $this->enforceExtension($options, $media);

        return $this->configServer($provider, $media)->cacheFileExists($provider->getFilesystemReference($media), $options);
    }

    public function getThumbPath(MediaProviderInterface $provider, MediaInterface $media, array $options): ?string
    {
        $options = $this->enforceExtension($options, $media);

        return $this->configServer($provider, $media)->getCachePath($provider->getFilesystemReference($media), $options);
    }

    protected function enforceExtension(array $options, MediaInterface $media): array
    {
        $options['fm'] = $options['fm'] ?? $media->getFileInfo()->getExt();

        return $options;
    }

    protected function configServer(MediaProviderInterface $provider, MediaInterface $media): Server
    {
        $filesystem = $provider->getFilesystem();
        $this->server->setSource($filesystem);
        $this->server->setCache($filesystem);
        $this->server->setCachePathPrefix($provider->generatePath($media));
        $this->server->setGroupCacheInFolders(false);

        return $this->server;
    }

    protected function extractSizes(string $path): iterable
    {
        $tmpFile = tmpfile();
        stream_copy_to_stream($this->server->getCache()->readStream($path), $tmpFile);
        $imageSize = getimagesize(stream_get_meta_data($tmpFile)['uri']);

        return [
            'width' => $imageSize[0],
            'height' => $imageSize[1],
        ];
    }
}
