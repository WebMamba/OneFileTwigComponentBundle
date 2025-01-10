<?php

namespace Webmamba\OneFileTwigComponentBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Webmamba\OneFileTwigComponentBundle\DependencyInjection\OneFileTwigComponentExtension;

class OneFileTwigComponentBundle extends Bundle
{
    public function createContainerExtension(): ?ExtensionInterface
    {
        return new OneFileTwigComponentExtension();
    }
}