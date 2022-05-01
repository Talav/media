<?php

namespace Talav\Component\Media\Tests\Functional\Setup\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Media\Model\FileInfo;
use Talav\Component\Media\Model\Media;

/**
 * Doctrine media entity that is used for testing.
 *
 * @ORM\Entity(repositoryClass="Talav\Component\Resource\Repository\ResourceRepository")
 * @ORM\Table(name="test_media")
 */
class MediaEntity extends Media
{
    /**
     * A unique ID.
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
     */
    public $id = null;

    /**
     * @ORM\Column(type="string", name="name", nullable=true)
     */
    public ?string $name = null;

    /**
     * @ORM\Column(type="string", name="description", nullable=true)
     */
    protected ?string $description = null;

    /**
     * @ORM\Column(type="string", name="context", nullable=true)
     */
    protected ?string $context = null;

    /**
     * @ORM\Column(type="string", name="provider_name", nullable=true)
     */
    protected ?string $providerName = null;

    /**
     * @ORM\Column(type="string", name="provider_reference", nullable=true)
     */
    protected ?string $providerReference = null;

    /**
     * @ORM\Column(type="json", name="thumbs_info", nullable=false)
     */
    protected ?array $thumbsInfo = [];

    /**
     * @ORM\Column(type="object", name="file_info", nullable=false)
     */
    protected ?FileInfo $fileInfo = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
