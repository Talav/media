<?php

declare(strict_types=1);

namespace Talav\Component\Media\Context;

use Talav\Component\Media\Provider\MediaProviderInterface;

/**
 * Configuration for each context.
 */
class ContextConfig
{
    protected string $name;

    /** @var iterable|MediaProviderInterface[] */
    protected iterable $providers;

    /** @var iterable|string[] */
    protected iterable $formats = [];

    public function __construct(string $name, iterable $providers, iterable $formats = [])
    {
        $this->name = $name;
        $this->providers = $providers;
        $this->formats = $formats;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /** @return MediaProviderInterface[]  */
    public function getProviders(): iterable
    {
        return $this->providers;
    }

    /** @return string[]  */
    public function getFormats(): iterable
    {
        return $this->formats;
    }
}
