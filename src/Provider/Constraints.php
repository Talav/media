<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

class Constraints
{
    /** @var array|string[] */
    protected $extensions = [];

    /** @var array|string[] */
    protected $fileConstraints = [];

    /** @var array|string[] */
    protected $imageConstraints = [];

    public function __construct($extensions, $fileConstraints, $imageConstraints)
    {
        $this->extensions = $extensions;
        $this->fileConstraints = $fileConstraints;
        $this->imageConstraints = $imageConstraints;
    }

    /**
     * @return array|string[]
     */
    public function getExtensions(): array
    {
        return $this->extensions;
    }

    public function isValidExtension(string $ext): bool
    {
        return count($this->extensions) == 0 || in_array($ext, $this->extensions);
    }

    public function isValidMimeType(string $ext): bool
    {
        if (!isset($this->fileConstraints['mimeTypes']) || !is_array($this->fileConstraints['mimeTypes'])) {
            return true;
        }

        return count($this->fileConstraints['mimeTypes']) == 0 || in_array($ext, $this->fileConstraints['mimeTypes']);
    }

    /**
     * @return array|string[]
     */
    public function getFileConstraints(): array
    {
        return $this->fileConstraints;
    }

    /**
     * @return array|string[]
     */
    public function getImageConstraints(): array
    {
        return $this->imageConstraints;
    }
}
