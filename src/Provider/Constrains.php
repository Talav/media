<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

class Constrains
{
    /** @var array|string[] */
    protected $extensions = [];

    /** @var array|string[] */
    protected $mimeTypes = [];

    public function __construct($extensions, $mimeTypes)
    {
        $this->extensions = $extensions;
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * @return array|string[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    /**
     * @return array|string[]
     */
    public function getMimeTypes(): array
    {
        return $this->mimeTypes;
    }

    public function isValidExtension(string $ext): bool
    {
        return count($this->extensions) == 0 || in_array($ext, $this->extensions);
    }

    public function isValidMimeType(string $type): bool
    {
        return count($this->mimeTypes) == 0 || in_array($type, $this->mimeTypes);
    }
}
