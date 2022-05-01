<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use Talav\Component\Media\Context\ContextConfig;

/**
 * Class to connect providers, security and contexts.
 */
class ProviderPool
{
    /** @var MediaProviderInterface[] */
    protected array $providers = [];

    protected array $contexts = [];

    public function getProvider(string $name): MediaProviderInterface
    {
        if (!$name) {
            throw new \InvalidArgumentException('Provider name cannot be empty, did you forget to call setProviderName() in your Media object?');
        }
        if (empty($this->providers)) {
            throw new \RuntimeException(sprintf('Unable to retrieve provider named "%s" since there are no providers configured yet.', $name));
        }
        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException(sprintf('Unable to retrieve the provider named "%s". Available providers are %s.', $name, '"'.implode('", "', $this->getProviderList()).'"'));
        }

        return $this->providers[$name];
    }

    /**
     * Adds context config.
     */
    public function addContext(ContextConfig $contextConfig): void
    {
        if ($this->hasContext($contextConfig->getName())) {
            throw new \RuntimeException(sprintf('Context "%s" has already been registered', $contextConfig->getName()));
        }
        $this->contexts[$contextConfig->getName()] = $contextConfig;
        foreach ($contextConfig->getProviders() as $provider) {
            if (!$this->hasProvider($provider->getName())) {
                $this->providers[$provider->getName()] = $provider;
            }
            foreach ($contextConfig->getFormats() as $formatName => $format) {
                $provider->addFormat($formatName, $format);
            }
        }
    }

    public function hasContext(string $name): bool
    {
        return isset($this->contexts[$name]);
    }

    public function getContext(string $name): ContextConfig
    {
        if (!$this->hasContext($name)) {
            throw new \RuntimeException(sprintf('Context "%s" does not exists', $name));
        }

        return $this->contexts[$name];
    }

    public function getContexts(): array
    {
        return $this->contexts;
    }

    public function hasProvider(string $name): bool
    {
        return isset($this->providers[$name]);
    }

    public function getProviderList(): array
    {
        $choices = [];
        foreach (array_keys($this->providers) as $name) {
            $choices[$name] = $name;
        }

        return $choices;
    }
}
