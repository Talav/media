<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Constraints
{
    /** @var string[] */
    protected iterable $extensions = [];

    /** @var string[] */
    protected iterable $fileConstraints = [];

    /** @var string[] */
    protected iterable $imageConstraints = [];

    public function __construct(iterable $extensions, iterable $fileConstraints = [], iterable $imageConstraints = [])
    {
        $this->extensions = $extensions;
        $this->fileConstraints = $fileConstraints;
        $this->imageConstraints = $imageConstraints;
    }

    public function getFieldConstraints(): array
    {
        $constraints = [
            new Constraint\Callback(
                function ($object, ExecutionContextInterface $context) {
                    if ($object instanceof UploadedFile) {
                        if (!$this->isValidExtension($object->getClientOriginalExtension())) {
                            $context->addViolation(
                                sprintf(
                                    'It\'s not allowed to upload a file with extension "%s"',
                                    $object->getClientOriginalExtension()
                                )
                            );
                        }
                    }
                }
            ),
            count($this->imageConstraints) > 0 ? new Constraint\Image(array_merge($this->fileConstraints, $this->imageConstraints)) : new Constraint\File($this->fileConstraints),
        ];

        return $constraints;
    }

    /**
     * Validates provided extension.
     */
    protected function isValidExtension(string $ext): bool
    {
        return 0 == count($this->extensions) || in_array($ext, $this->extensions);
    }
}
