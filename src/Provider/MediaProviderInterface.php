<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use League\Flysystem\FilesystemInterface;
use Talav\Component\Media\Model\MediaInterface;

interface MediaProviderInterface
{
    /**
     * Returns provider name
     */
    public function getName(): string;

    public function getFilesystem(): FilesystemInterface;

    public function prePersist(MediaInterface $media): void;

    public function preUpdate(MediaInterface $media): void;

    public function preRemove(MediaInterface $media): void;

    public function postUpdate(MediaInterface $media): void;

    public function postRemove(MediaInterface $media): void;

    public function postPersist(MediaInterface $media): void;
}
