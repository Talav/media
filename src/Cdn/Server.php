<?php

declare(strict_types=1);

namespace Talav\Component\Media\Cdn;

class Server implements CdnInterface
{
    /** @var string */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath($relativePath, $isFlushable)
    {
        return sprintf('%s/%s', rtrim($this->path, '/'), ltrim($relativePath, '/'));
    }

    /**
     * {@inheritdoc}
     */
    public function flushByString($string)
    {
        // nothing to do
    }

    /**
     * {@inheritdoc}
     */
    public function flush($string)
    {
        // nothing to do
    }

    /**
     * {@inheritdoc}
     */
    public function flushPaths(array $paths)
    {
        // nothing to do
    }

    /**
     * {@inheritdoc}
     */
    public function getFlushStatus($identifier)
    {
        // nothing to do
    }
}
