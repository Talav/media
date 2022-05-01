<?php

declare(strict_types=1);

namespace Talav\Component\Media\Tests\Unit\Provider;

use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use PHPUnit\Framework\TestCase;
use Talav\Component\Media\Cdn\Server;
use Talav\Component\Media\Context\ContextConfig;
use Talav\Component\Media\Generator\UuidGenerator;
use Talav\Component\Media\Provider\Constraints;
use Talav\Component\Media\Provider\FileProvider;
use Talav\Component\Media\Provider\ProviderPool;

final class ProviderPoolTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_if_provider_name_is_empty()
    {
        $providerPool = new ProviderPool();
        $this->expectException(\InvalidArgumentException::class);
        $providerPool->getProvider('');
    }

    /**
     * @test
     */
    public function it_throws_runtime_exception_if_provider_list_is_empty()
    {
        $providerPool = new ProviderPool();
        $this->expectException(\RuntimeException::class);
        $providerPool->getProvider('test');
    }

    /**
     * @test
     */
    public function it_throws_invalid_argument_exception_if_provider_key_does_not_exists()
    {
        $context = new ContextConfig('config', $this->createProviders(['test']));
        $providerPool = new ProviderPool();
        $providerPool->addContext($context);
        $this->expectException(\InvalidArgumentException::class);
        $providerPool->getProvider('test 22');
    }

    /**
     * @test
     */
    public function it_correctly_returns_provider_by_key()
    {
        $providerName = 'test';
        $context = new ContextConfig('config', $this->createProviders(['test']));
        $providerPool = new ProviderPool();
        $providerPool->addContext($context);
        $provider = $providerPool->getProvider($providerName);
        $this->assertInstanceOf(FileProvider::class, $provider);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_override_context()
    {
        $context1 = new ContextConfig('config', $this->createProviders(['test']));
        $context2 = new ContextConfig('config', $this->createProviders(['test']));
        $providerPool = new ProviderPool();
        $providerPool->addContext($context1);
        $this->expectException(\RuntimeException::class);
        $providerPool->addContext($context2);
    }

    /**
     * @test
     */
    public function it_throws_runtime_exception_if_config_key_does_not_exists()
    {
        $context = new ContextConfig('config', $this->createProviders(['test']));
        $providerPool = new ProviderPool();
        $providerPool->addContext($context);
        $this->expectException(\RuntimeException::class);
        $providerPool->getContext('config 22');
    }

    /**
     * @test
     */
    public function it_correctly_returns_context_by_name()
    {
        $context = new ContextConfig('config', $this->createProviders(['test']));
        $providerPool = new ProviderPool();
        $providerPool->addContext($context);
        $context = $providerPool->getContext('config');
        $this->assertNotNull($context);
    }

    /**
     * @test
     */
    public function it_returns_all_added_contexts()
    {
        $context1 = new ContextConfig('config1', $this->createProviders(['test']));
        $context2 = new ContextConfig('config2', $this->createProviders(['test']));
        $providerPool = new ProviderPool();
        $providerPool->addContext($context1);
        $providerPool->addContext($context2);
        $this->assertCount(2, $providerPool->getContexts());
    }

    protected function createProviders(iterable $providerNames): iterable
    {
        $fs = new Filesystem(new InMemoryFilesystemAdapter());
        $cdn = new Server(sys_get_temp_dir());
        $generator = new UuidGenerator();
        $return = [];
        foreach ($providerNames as $providerName) {
            $return[] = new FileProvider($providerName, $fs, $cdn, $generator, new Constraints(['txt'], ['mimeTypes' => [
                'text/plain',
            ]], []));
        }

        return $return;
    }
}
