<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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

    /** @var Constraints */
    protected $constrains;

    /** @var MediaInterface[] */
    private $clones = [];

    public function __construct(
        string $name,
        FilesystemInterface $filesystem,
        CdnInterface $cdn,
        GeneratorInterface $generator,
        ?Constraints $constrains = null
    ) {
        $this->name = $name;
        $this->filesystem = $filesystem;
        $this->cdn = $cdn;
        $this->generator = $generator;
        if (null === $constrains) {
            $constrains = new Constraints([], []);
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
        $this->validateMedia($media);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(MediaInterface $media): void
    {
        // validate media file
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
     * {@inheritdoc}
     */
    public function getFileFieldConstraints(): array
    {
        return [
            new Constraint\File($this->constrains->getFileConstraints()),
            new Constraint\Callback([$this, 'validateExtension']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function validateExtension($object, ExecutionContextInterface $context)
    {
        if ($object instanceof UploadedFile) {
            if (!$this->constrains->isValidExtension($object->getClientOriginalExtension())) {
                $context->addViolation(
                    sprintf(
                        'It\'s not allowed to upload a file with extension "%s"',
                        $object->getClientOriginalExtension()
                    )
                );
            }
        }
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
        if (empty($media->getContext())) {
            throw new InvalidMediaException('Media should have context defined');
        }
        if (empty($media->getProviderName())) {
            throw new InvalidMediaException('Media should have provider defined');
        }
        if (empty($media->getName())) {
            throw new InvalidMediaException('Media should have name defined');
        }
        // all other checks only applicable if a new file is uploaded
        if (null === $media->getFile()) {
            return;
        }
        if (!$this->constrains->isValidExtension($media->getFileExtension())) {
            throw new InvalidMediaException('Invalid file extension');
        }
        if (!$this->constrains->isValidMimeType($media->getMimeType())) {
            throw new InvalidMediaException('Invalid file mime type');
        }
    }
}
