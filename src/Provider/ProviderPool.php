<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use Talav\Component\Media\Context\ContextConfig;

/**
 * Class to connect providers, security and contexts
 */
class ProviderPool
{
    /** @var MediaProviderInterface[] */
    protected $providers = [];

    /** @var array */
    protected $contexts = [];

    /**
     * @param string $name
     *
     * @throws \RuntimeException
     */
    public function getProvider($name): MediaProviderInterface
    {
        if (!$name) {
            throw new \InvalidArgumentException('Provider name cannot be empty, did you forget to call setProviderName() in your Media object?');
        }
        if (empty($this->providers)) {
            throw new \RuntimeException(sprintf('Unable to retrieve provider named "%s" since there are no providers configured yet.', $name));
        }
        if (!isset($this->providers[$name])) {
            throw new \InvalidArgumentException(sprintf('Unable to retrieve the provider named "%s". Available providers are %s.', $name, '"' . implode('", "', $this->getProviderList()) . '"'));
        }

        return $this->providers[$name];
    }

    /**
     * Adds context config
     */
    public function addContext(ContextConfig $contextConfig): void
    {
        if ($this->hasContext($contextConfig->getName())) {
            throw new \RuntimeException(sprintf('Context "%s" has already been registered'));
        }
        $this->contexts[$contextConfig->getName()] = $contextConfig;
        if (!$this->hasContext($contextConfig->getProvider()->getName())) {
            $this->providers[$contextConfig->getProvider()->getName()] = $contextConfig->getProvider();
        }
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasContext($name)
    {
        return isset($this->contexts[$name]);
    }

    /**
     * @param string $name
     *
     * @return array|null
     */
    public function getContext($name)
    {
        if (!$this->hasContext($name)) {
            throw new \RuntimeException(sprintf('Context "%s" does not exists'));
        }

        return $this->contexts[$name];
    }

    /**
     * @return array
     */
    public function getProviderList()
    {
        $choices = [];
        foreach (array_keys($this->providers) as $name) {
            $choices[$name] = $name;
        }

        return $choices;
    }

    /**
     * Returns the context list.
     *
     * @return array
     */
    public function getContexts()
    {
        return $this->contexts;
    }
}
