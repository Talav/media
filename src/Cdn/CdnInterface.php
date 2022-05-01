<?php

declare(strict_types=1);

namespace Talav\Component\Media\Cdn;

interface CdnInterface
{
    public const STATUS_OK = 1;

    public const STATUS_TO_SEND = 2;

    public const STATUS_TO_FLUSH = 3;

    public const STATUS_ERROR = 4;

    public const STATUS_WAITING = 5;

    /**
     * Return the base path.
     */
    public function getPath(string $relativePath): string;

    /**
     * Flush the resource.
     */
    public function flush(string $string): int;
}
