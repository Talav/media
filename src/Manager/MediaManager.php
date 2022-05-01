<?php

declare(strict_types=1);

namespace Talav\Component\Media\Manager;

use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Resource\Manager\ResourceManager;
use Talav\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class MediaManager extends ResourceManager
{
    /**
     * {@inheritdoc}
     */
    public function add(ResourceInterface $resource): void
    {
        Assert::isInstanceOf($resource, MediaInterface::class);
        parent::add($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function update(ResourceInterface $resource, $flush = false): void
    {
        Assert::isInstanceOf($resource, MediaInterface::class);
        parent::update($resource, $flush);
    }

    /**
     * {@inheritdoc}
     */
    public function remove(ResourceInterface $resource): void
    {
        Assert::isInstanceOf($resource, MediaInterface::class);
        parent::remove($resource);
    }
}
