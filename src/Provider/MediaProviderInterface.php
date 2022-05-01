<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Talav\Component\Media\Model\MediaInterface;

interface MediaProviderInterface
{
    /**
     * Returns provider name.
     */
    public function getName(): string;

    public function getFilesystem(): FilesystemOperator;

    public function generatePath(MediaInterface $media): string;

    public function getFilesystemReference(MediaInterface $media): string;

    public function getMediaContent(MediaInterface $media): string;

    public function setMediaContent(MediaInterface $media, UploadedFile $file): void;

    public function deleteMediaContent(MediaInterface $media): void;

    public function getFileFieldConstraints(): array;

    public function addFormat(string $name, array $options): void;

    public function getFormat($name): array;

    public function getFormats(): array;

    public function getPublicUrl(MediaInterface $media): string;

    public function getTemplateConfig(): ?TemplateConfig;

    public function setTemplateConfig(TemplateConfig $templateConfig): void;
}
