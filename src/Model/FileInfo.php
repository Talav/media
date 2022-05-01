<?php

declare(strict_types=1);

namespace Talav\Component\Media\Model;

class FileInfo
{
    protected int $size;

    protected string $mimeType;

    protected string $ext;

    protected string $name;

    public function __construct($size, $mimeType, $ext, $name)
    {
        $this->size = $size;
        $this->mimeType = $mimeType;
        $this->ext = $ext;
        $this->name = $name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getExt(): string
    {
        return $this->ext;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
