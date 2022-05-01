<?php

declare(strict_types=1);

namespace Talav\Component\Media\Mapper\Configurator;

use AutoMapperPlus\AutoMapperPlusBundle\AutoMapperConfiguratorInterface;
use AutoMapperPlus\Configuration\AutoMapperConfigInterface;
use Talav\Component\Media\Mapper\Dto\CreateMediaMapper;
use Talav\Component\Media\Mapper\Dto\UpdateMediaMapper;
use Talav\Component\Media\Message\Dto\Media\CreateMediaDto;
use Talav\Component\Media\Message\Dto\Media\UpdateMediaDto;
use Talav\Component\Media\Model\MediaInterface;

class MediaConfigurator implements AutoMapperConfiguratorInterface
{
    public function configure(AutoMapperConfigInterface $config): void
    {
        $config->getOptions()->createUnregisteredMappings();
        $config->registerMapping(CreateMediaDto::class, MediaInterface::class)
            ->useCustomMapper(new CreateMediaMapper());
        $config->registerMapping(UpdateMediaDto::class, MediaInterface::class)
            ->useCustomMapper(new UpdateMediaMapper());
    }
}
