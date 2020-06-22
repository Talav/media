<?php

declare(strict_types=1);

namespace Talav\Component\Media\Model;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Talav\Component\Resource\Model\ResourceInterface;

interface MediaInterface extends ResourceInterface
{
    public function setFile(File $file): void;

    public function getFile(): ?UploadedFile;

    public function resetFile(): void;

    public function getFileName(): ?string;

    public function setFileName(?string $fileName): void;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getDescription(): ?string;

    public function setDescription(?string $description): void;

    public function getContext(): ?string;

    public function setContext(?string $context): void;

    public function getProviderName(): ?string;

    public function setProviderName(?string $providerName): void;

    public function getProviderReference(): ?string;

    public function setProviderReference(?string $providerReference): void;

    public function getPreviousProviderReference(): ?string;

    public function setPreviousProviderReference(?string $previousProviderReference): void;

    public function getSize(): ?int;

    public function setSize(?int $size): void;

    public function getMimeType(): ?string;

    public function setMimeType(?string $mimeType): void;

    public function getFileExtension(): ?string;

    public function setFileExtension(?string $fileExtension): void;
}
