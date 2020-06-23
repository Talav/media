<?php

declare(strict_types=1);

namespace Talav\Component\Media\Model;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Talav\Component\Resource\Model\ResourceTrait;
use Talav\Component\Resource\Model\Timestampable;

class Media implements MediaInterface
{
    use ResourceTrait;
    use Timestampable;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $description;

    /** @var string|null */
    protected $context;

    /** @var string|null */
    protected $providerName;

    /** @var string|null */
    protected $providerReference;

    /** @var int|null */
    protected $size;

    /**
     * Mime type of the new file
     *
     * @var string|null
     */
    protected $mimeType;

    /**
     * File extension
     *
     * @var string|null
     */
    protected $fileExtension;

    /**
     * File name
     *
     * @var string|null
     */
    protected $fileName;

    /** @var UploadedFile|null */
    protected $file;

    /** @var string|null */
    protected $previousProviderReference;

    public function setFile(File $file): void
    {
        $this->file = $file;
        $this->mimeType = $file->getMimeType();
        $this->size = $file->getSize();
        $this->fileExtension = $file->getExtension();
        $this->previousProviderReference = $this->providerReference;
        $this->providerReference = null;
        $this->fileName = $file->getClientOriginalName();
        $this->ensureProviderReference();
        $this->fixName();
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function resetFile(): void
    {
        $this->file = null;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): void
    {
        $this->fileName = $fileName;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): void
    {
        $this->context = $context;
    }

    public function getProviderName(): ?string
    {
        return $this->providerName;
    }

    public function setProviderName(?string $providerName): void
    {
        $this->providerName = $providerName;
    }

    public function getProviderReference(): ?string
    {
        return $this->providerReference;
    }

    public function setProviderReference(?string $providerReference): void
    {
        $this->providerReference = $providerReference;
    }

    public function getPreviousProviderReference(): ?string
    {
        return $this->previousProviderReference;
    }

    public function setPreviousProviderReference(?string $previousProviderReference): void
    {
        $this->previousProviderReference = $previousProviderReference;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): void
    {
        $this->size = $size;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getFileExtension(): ?string
    {
        return $this->fileExtension;
    }

    public function setFileExtension(?string $fileExtension): void
    {
        $this->fileExtension = $fileExtension;
    }

    protected function ensureProviderReference(): void
    {
        // this is the name used to store the file
        if (!$this->getProviderReference() ||
            MediaInterface::MISSING_BINARY_REFERENCE === $this->getProviderReference()
        ) {
            $this->setProviderReference($this->generateReferenceName());
        }
    }

    protected function generateReferenceName(): string
    {
        return sha1($this->getName() . uniqid() . random_int(11111, 99999));
    }

    /**
     * Fixes media name if it's not provided
     */
    protected function fixName(): void
    {
        if (null !== $this->getFile() && null === $this->name) {
            $this->name = $this->getFile()->getClientOriginalName();
        }
    }
}
