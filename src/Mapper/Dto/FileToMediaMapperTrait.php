<?php

declare(strict_types=1);

namespace Talav\Component\Media\Mapper\Dto;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Talav\Component\Media\Model\FileInfo;
use Talav\Component\Media\Model\MediaInterface;

trait FileToMediaMapperTrait
{
    public function mapFileToMedia(UploadedFile $source, MediaInterface $destination): void
    {
        $destination->setFileInfo(new FileInfo(
            $source->getSize(),
            $source->getMimeType(),
            $source->getClientOriginalExtension(),
            $source->getClientOriginalName()
        ));

        // name is required, try to fill it
        if (is_null($destination->getName())) {
            $destination->setName($source->getClientOriginalName());
        }
    }
}
