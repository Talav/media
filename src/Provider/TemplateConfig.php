<?php

declare(strict_types=1);

namespace Talav\Component\Media\Provider;

class TemplateConfig
{
    // Template to render thumbnail
    protected string $thumb;

    // Template to render media view
    protected string $view;

    public function __construct(string $thumb, string $view)
    {
        $this->thumb = $thumb;
        $this->view = $view;
    }

    public function getThumb(): string
    {
        return $this->thumb;
    }

    public function getView(): string
    {
        return $this->view;
    }
}
