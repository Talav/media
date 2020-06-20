<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use League\Flysystem\FilesystemInterface;
use Talav\Component\Media\Cdn\CdnInterface;
use Talav\Component\Media\Exception\InvalidMediaException;
use Talav\Component\Media\Generator\GeneratorInterface;
use Talav\Component\Media\Model\MediaInterface;

class FileProvider implements MediaProviderInterface
{
    /** @var string */
    protected $name;

    /** @var FilesystemInterface */
    protected $filesystem;

    /** @var CdnInterface */
    protected $cdn;

    /** @var GeneratorInterface */
    protected $generator;

    /** @var Constrains */
    protected $constrains;

    /** @var MediaInterface[] */
    private $clones = [];

    public function __construct(
        string $name,
        FilesystemInterface $filesystem,
        CdnInterface $cdn,
        GeneratorInterface $generator,
        ?Constrains $constrains = null
    ) {
        $this->name = $name;
        $this->filesystem = $filesystem;
        $this->cdn = $cdn;
        $this->generator = $generator;
        if (null === $constrains) {
            $constrains = new Constrains([], []);
        }
        $this->constrains = $constrains;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(MediaInterface $media): void
    {
        // validate media file
        if (null === $media->getFile()) {
            return;
        }
        $this->validateMedia($media);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(MediaInterface $media): void
    {
        // validate media file
        if (null === $media->getFile()) {
            return;
        }
        $this->validateMedia($media);
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove(MediaInterface $media): void
    {
        // clone image to process files int postRemove
        $hash = spl_object_hash($media);
        $this->clones[$hash] = clone $media;
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate(MediaInterface $media): void
    {
        if (null === $media->getFile()) {
            return;
        }

        // Delete the current file from the FS
        $oldMedia = clone $media;
        // if no previous reference is provided, it prevents
        // Filesystem from trying to remove a directory
        if (null !== $media->getPreviousProviderReference()) {
            $oldMedia->setProviderReference($media->getPreviousProviderReference());
            $this->deletePath($this->getFilesystemReference($oldMedia));
        }
        $this->copyTempFile($media);
        $media->resetFile();
    }

    /**
     * {@inheritdoc}
     */
    public function postRemove(MediaInterface $media): void
    {
        $hash = spl_object_hash($media);

        if (isset($this->clones[$hash])) {
            $media = $this->clones[$hash];
            unset($this->clones[$hash]);
        }
        $this->deletePath($this->getFilesystemReference($media));
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist(MediaInterface $media): void
    {
        if (null === $media->getFile()) {
            return;
        }
        $this->copyTempFile($media);
        $media->resetFile();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystem(): FilesystemInterface
    {
        return $this->filesystem;
    }

    /**
     * {@inheritdoc}
     */
    final public function transform(MediaInterface $media): void
    {
        if (null === $media->getBinaryContent()) {
            return;
        }

        $this->doTransform($media);
        $this->flushCdn($media);
    }

    /**
     * {@inheritdoc}
     */
    public function generatePath(MediaInterface $media): string
    {
        return $this->generator->generatePath($media);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystemReference(MediaInterface $media): string
    {
        return sprintf(
            '%s/%s',
            $this->generatePath($media),
            $media->getProviderReference()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaContent(MediaInterface $media): string
    {
        return $this->getFilesystem()->read($this->getFilesystemReference($media));
    }

    /**
     * Set the file contents for an media.
     */
    protected function copyTempFile(MediaInterface $media): void
    {
        $this->getFilesystem()->put(
            $this->getFilesystemReference($media),
            file_get_contents($media->getFile()->getRealPath())
        );
    }

    /**
     * Delete file if it exists
     */
    protected function deletePath(string $path): void
    {
        if ($this->getFilesystem()->has($path)) {
            $this->getFilesystem()->delete($path);
        }
    }

    /**
     * Make sure media is valid
     */
    protected function validateMedia(MediaInterface $media): void
    {
        if (!$this->constrains->isValidExtension($media->getFileExtension())) {
            throw new InvalidMediaException('Invalid file extension');
        }
        if (!$this->constrains->isValidMimeType($media->getMimeType())) {
            throw new InvalidMediaException('Invalid file mime type');
        }
    }
}
