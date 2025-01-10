<?php

namespace Webmamba\OneFileTwigComponentBundle;

class CompiledComponent
{
    public function __construct(
        private string $content,
        private ?string $twig,
        private ?string $css,
        private ?string $js,
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTwig(): ?string
    {
        return $this->twig;
    }

    public function getCSS(): ?string
    {
        return $this->css;
    }

    public function getJs(): ?string
    {
        return $this->js;
    }
}