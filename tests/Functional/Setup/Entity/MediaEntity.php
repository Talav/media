<?php

namespace Talav\Component\Media\Tests\Functional\Setup\Entity;

use Doctrine\ORM\Mapping as ORM;
use Talav\Component\Media\Model\Media;
use Talav\Component\Resource\Model\ResourceInterface;

/**
 * Doctrine media entity that is used for testing.
 *
 * @ORM\Entity
 * @ORM\Table(name="test_media")
 */
class MediaEntity extends Media
{
    /**
     * A unique ID.
     *
     * @var integer|null
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
     */
    public $id = null;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="name", nullable=true)
     */
    public $name;

    /**
     * @var string
     * @ORM\Column(type="string", name="description", nullable=true)
     */
    protected $description;

    /**
     * @var string
     * @ORM\Column(type="string", name="context", nullable=true)
     */
    protected $context;

    /**
     * @var string
     * @ORM\Column(type="string", name="provider_name", nullable=true)
     */
    protected $providerName;

    /**
     * @var int
     * @ORM\Column(type="integer", name="provider_status", nullable=true)
     */
    protected $providerStatus;

    /**
     * @var string
     * @ORM\Column(type="string", name="provider_reference", nullable=true)
     */
    protected $providerReference;

    /**
     * @var int
     * @ORM\Column(type="integer", name="size", nullable=true)
     */
    protected $size;

    /**
     * Mime type of the new file
     *
     * @var string
     * @ORM\Column(type="string", name="mime_type", nullable=true)
     */
    protected $mimeType;

    /**
     * File extension
     *
     * @var string
     * @ORM\Column(type="string", name="file_extension", nullable=true)
     */
    protected $fileExtension;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}