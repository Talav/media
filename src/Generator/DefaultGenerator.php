<?php

declare(strict_types=1);

namespace Sonata\MediaBundle\Generator;

namespace Talav\Component\Media\Generator;

use Talav\Component\Media\Model\MediaInterface;

class DefaultGenerator implements GeneratorInterface
{
    /** @var int */
    protected $firstLevel;

    /** @var int */
    protected $secondLevel;

    /**
     * @param int $firstLevel
     * @param int $secondLevel
     */
    public function __construct($firstLevel = 100000, $secondLevel = 1000)
    {
        $this->firstLevel = $firstLevel;
        $this->secondLevel = $secondLevel;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePath(MediaInterface $media)
    {
        $id = md5((string) $media->getId());

        return sprintf('%s/%04s/%04s', $media->getContext(), substr($id, 0, 4), substr($id, 4, 2));
    }
}
