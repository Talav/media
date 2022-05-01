<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Talav\Component\Media\Cdn\CdnInterface;
use Talav\Component\Media\Generator\GeneratorInterface;
use Talav\Component\Media\Model\MediaInterface;

class FileProvider implements MediaProviderInterface
{
    protected string $name;

    protected FilesystemOperator $filesystem;

    protected CdnInterface $cdn;

    protected GeneratorInterface $generator;

    protected Constraints $constrains;

    protected ?TemplateConfig $templateConfig = null;

    protected array $formats = [];

    public function __construct(
        string $name,
        FilesystemOperator $filesystem,
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getFilesystem(): FilesystemOperator
    {
        return $this->filesystem;
    }

    public function generatePath(MediaInterface $media): string
    {
        return $this->generator->generatePath($media);
    }

    public function getFilesystemReference(MediaInterface $media): string
    {
        return sprintf('%s/%s', $this->generatePath($media), $media->getProviderReference());
    }

    public function getMediaContent(MediaInterface $media): string
    {
        return $this->getFilesystem()->read($this->getFilesystemReference($media));
    }

    public function setMediaContent(MediaInterface $media, UploadedFile $file): void
    {
        $this->getFilesystem()->write(
            $this->getFilesystemReference($media),
            file_get_contents($file->getRealPath())
        );
    }

    public function deleteMediaContent(MediaInterface $media): void
    {
        $path = $this->getFilesystemReference($media);
        if ($this->getFilesystem()->fileExists($path)) {
            $this->getFilesystem()->delete($path);
        }
        $this->getFilesystem()->delete($this->getFilesystemReference($media));
    }

    public function getFileFieldConstraints(): array
    {
        return $this->constrains->getFieldConstraints();
    }

    public function addFormat(string $name, array $options): void
    {
        $this->formats[$name] = $options;
    }

    public function getFormat($name): array
    {
        if (!isset($this->formats[$name])) {
            throw new \RuntimeException('Format is not found');
        }

        return $this->formats[$name];
    }

    public function getFormats(): array
    {
        return $this->formats;
    }

    public function getPublicUrl(MediaInterface $media): string
    {
        return $this->cdn->getPath($this->getFilesystemReference($media));
    }

    public function getTemplateConfig(): ?TemplateConfig
    {
        return $this->templateConfig;
    }

    public function setTemplateConfig(TemplateConfig $templateConfig): void
    {
        $this->templateConfig = $templateConfig;
    }
}
