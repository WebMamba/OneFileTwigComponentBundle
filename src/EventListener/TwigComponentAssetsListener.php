<?php

namespace Webmamba\OneFileTwigComponentBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\UX\TwigComponent\ComponentAttributes;
use Symfony\UX\TwigComponent\Event\PreRenderEvent;
use Twig\Environment;
use Webmamba\OneFileTwigComponentBundle\AssetsComponentRegistry;
use Webmamba\OneFileTwigComponentBundle\CompiledComponent;
use Webmamba\OneFileTwigComponentBundle\TwigComponentAssetExtractor;
use Webmamba\OneFileTwigComponentBundle\TwigComponentAssetsCompiler;

class TwigComponentAssetsListener implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private TwigComponentAssetsCompiler $compiler,
        private AssetsComponentRegistry $assetRegistry,
    ) {}
    public static function getSubscribedEvents(): array
    {
        return [
            PreRenderEvent::class => ['onPreRender'],
        ];
    }

    public function onPreRender(PreRenderEvent $event): void
    {
        $template = $this->twig->load($event->getTemplate())->getSourceContext();
        $code = $template->getCode();

        if (!TwigComponentAssetExtractor::checkContainJavascriptOrCss($code)) {
            return;
        }

        $compiledComponent = $this->compiler->compile($event->getMountedComponent()->getName(), $code);

        $variables = $event->getVariables();
        $variables['attributes'] = $variables['attributes']->defaults($this->createComponentAttributes($compiledComponent, $event->getMountedComponent()->getName()));

        $event->setVariables($variables);

        if (null !== $compiledComponent->getTwig()) {
            $event->setTemplate($compiledComponent->getTwig());
        }

        if (null !== $compiledComponent->getTwig()) {
            $this->assetRegistry->add($compiledComponent->getTwig());
        }

        if (null !== $compiledComponent->getCSS()) {
            $this->assetRegistry->add($compiledComponent->getCSS());
        }

        if (null !== $compiledComponent->getJS()) {
            $this->assetRegistry->add($compiledComponent->getJS());
        }
    }

    private function createComponentAttributes(CompiledComponent $compiledComponent, string $componentName): ComponentAttributes
    {
        $attributes = [];

        if (null !== $compiledComponent->getCSS()) {
            $attributes[TwigComponentAssetsCompiler::CSS_ATTRIBUTE_ID] = $componentName;
        }

        if (null !== $compiledComponent->getJS()) {
            $controllerName = str_replace(':', '_', strtolower($componentName));
            $attributes['data-ux-component-id'] =  $controllerName;
            $attributes['data-controller'] =  $controllerName;
            $attributes['data-ux-component-controller-files'] = '../../component-assets/' . $componentName . '.js';
        }

        return new ComponentAttributes($attributes);
    }
}