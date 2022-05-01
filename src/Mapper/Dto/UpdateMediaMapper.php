<?php

declare(strict_types=1);

namespace Talav\Component\Media\Mapper\Dto;

use AutoMapperPlus\CustomMapper\CustomMapper;
use Talav\Component\Media\Message\Dto\Media\CreateMediaDto;
use Talav\Component\Media\Message\Dto\Media\UpdateMediaDto;
use Talav\Component\Media\Model\MediaInterface;
use Webmozart\Assert\Assert;

final class UpdateMediaMapper extends CustomMapper
{
    use FileToMediaMapperTrait;

    public function mapToObject($source, $destination)
    {
        Assert::isInstanceOf($source, UpdateMediaDto::class, MediaInterface::class);
        Assert::isInstanceOf($destination, MediaInterface::class);
        /* @var CreateMediaDto $source */
        /* @var MediaInterface $destination */

        if (!is_null($source->name)) {
            $destination->setName($source->name);
        }
        $destination->setDescription($source->description);

        if (!is_null($source->file)) {
            $this->mapFileToMedia($source->file, $destination);
            $destination->setProviderReference(null);
            $destination->getProviderReference();
        }

        return $destination;
    }
}
