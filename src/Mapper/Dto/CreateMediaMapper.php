<?php

declare(strict_types=1);

namespace Talav\Component\Media\Mapper\Dto;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Talav\Component\Media\Message\Dto\Media\CreateMediaDto;
use Talav\Component\Media\Model\MediaInterface;
use Webmozart\Assert\Assert;

final class CreateMediaMapper extends CustomMapper
{
    use FileToMediaMapperTrait;

    public function mapToObject($source, $destination)
    {
        Assert::isInstanceOf($source, CreateMediaDto::class, MediaInterface::class);
        Assert::isInstanceOf($destination, MediaInterface::class);
        /* @var CreateMediaDto $source */
        /* @var MediaInterface $destination */

        $destination->setName($source->name);
        $destination->setDescription($source->description);
        $destination->setProviderName($source->provider);
        $destination->setContext($source->context);

        $this->mapFileToMedia($source->file, $destination);

        // need to find a better place to generate reference
        $destination->getProviderReference();

        return $destination;
    }
}
