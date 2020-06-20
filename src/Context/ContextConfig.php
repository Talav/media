<?php

declare(strict_types=1);

namespace Talav\Component\Media\Context;

use Talav\Component\Media\Provider\MediaProviderInterface;

/**
 * Configuration for each context
 */
class ContextConfig
{
    /** @var string */
    protected $name;

    /** @var MediaProviderInterface */
    protected $provider = [];

    /** @var array|string[] */
    protected $formats = [];

    public function __construct(string $name, MediaProviderInterface $provider, array $formats = [])
    {
        $this->name = $name;
        $this->provider = $provider;
        $this->formats = $formats;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProvider(): MediaProviderInterface
    {
        return $this->provider;
    }

    public function getFormats(): array
    {
        return $this->formats;
    }
}
