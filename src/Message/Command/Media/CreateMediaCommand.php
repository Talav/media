<?php

declare(strict_types=1);

namespace Talav\Component\Media\Message\Command\Media;

use Talav\Component\Media\Message\Dto\Media\CreateMediaDto;
use Talav\Component\Resource\Model\DomainEventInterface;

final class CreateMediaCommand implements DomainEventInterface
{
    private CreateMediaDto $dto;

    public function __construct(CreateMediaDto $dto)
    {
        $this->dto = $dto;
    }

    public function getDto(): CreateMediaDto
    {
        return $this->dto;
    }
}
