<?php

declare(strict_types=1);

namespace Talav\Component\Media\Cdn;

class Server implements CdnInterface
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function getPath(string $relativePath): string
    {
        return sprintf('%s/%s', rtrim($this->path, '/'), ltrim($relativePath, '/'));
    }

    public function flush(string $string): int
    {
        return CdnInterface::STATUS_OK;
    }
}
