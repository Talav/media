<?php

declare(strict_types=1);

namespace Talav\Component\Media\Model;

use Talav\Component\Resource\Model\ResourceInterface;

interface MediaInterface extends ResourceInterface
{
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

    public function getThumbsInfo(): array;

    public function setThumbsInfo(array $thumbsInfo): void;

    public function setFileInfo(?FileInfo $fileInfo): void;

    public function getFileInfo(): ?FileInfo;
}
