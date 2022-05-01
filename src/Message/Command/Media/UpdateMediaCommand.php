<?php

declare(strict_types=1);

namespace Talav\Component\Media\Message\Command\Media;

use Talav\Component\Media\Message\Dto\Media\UpdateMediaDto;
use Talav\Component\Media\Model\MediaInterface;
use Talav\Component\Resource\Model\DomainEventInterface;

final class UpdateMediaCommand implements DomainEventInterface
{
    private MediaInterface $media;

    private UpdateMediaDto $dto;

    public function __construct(MediaInterface $media, UpdateMediaDto $dto)
    {
        $this->dto = $dto;
        $this->media = $media;
    }

    public function getDto(): UpdateMediaDto
    {
        return $this->dto;
    }

    public function getMedia(): MediaInterface
    {
        return $this->media;
    }
}
