<?php

namespace Webmamba\OneFileTwigComponentBundle;

use Symfony\Contracts\Cache\CacheInterface;
use Twig\Environment;

class TwigComponentAssetsCompiler
{
    const CSS_ATTRIBUTE_ID = 'ux-component-style';

    public function __construct(
        private CacheInterface $cache,
        private TwigComponentAssetExtractor $assetExtractor,
        private string $directoryPath
    ) {}

    public function compile(string $componentName, string $code): CompiledComponent
    {
        /** @var CompiledComponent $compiledComponent */
        $compiledComponent = $this->cache->get($componentName, function () use ($code, $componentName) {
            return new CompiledComponent(
                $code,
                $this->compileTwig($code, $componentName),
                $this->compileCSS($code, $componentName),
                $this->compileJS($code, $componentName)
            );
        });

        if ($compiledComponent->getContent() !== $code) {
            $this->cache->delete($componentName);

            /** @var CompiledComponent $compiledComponent */
            $compiledComponent = $this->cache->get($componentName, function () use ($code, $componentName) {
                return new CompiledComponent(
                    $code,
                    $this->compileTwig($code, $componentName),
                    $this->compileCSS($code, $componentName),
                    $this->compileJS($code, $componentName)
                );
            });
        }

        return $compiledComponent;
    }

    private function compileCSS(string $content, string $name): ?string
    {
        $extractedCSS = $this->assetExtractor->extractCSS($content);

        if (null === $extractedCSS) {
            return null;
        }

        $contentTemplate = <<<EOF
%s {
    %s
}
EOF;

        $fileName = $name.'.css';
        $filePath = $this->directoryPath .'/' . $fileName;

        $attributes = '['.self::CSS_ATTRIBUTE_ID.'='.$name.']';

        $contentFile = sprintf($contentTemplate, $attributes, $extractedCSS);

        file_put_contents($filePath, $contentFile);

        return $fileName;
    }

    private function compileJS(string $content, string $name): ?string
    {
        $extractedJS = $this->assetExtractor->extractJavaScript($content);
        if (null === $extractedJS) {
            return null;
        }

        $fileName = $name.'.js';
        $filePath = $this->directoryPath .'/' . $fileName;

        file_put_contents($filePath, $extractedJS);

        return $fileName;
    }

    private function compileTwig(string $content, string $name): ?string
    {
        $fileName = $name.'.html.twig';
        $extractedAsset = $this->assetExtractor->extractTwig($content);

        if (null === $extractedAsset) {
            return null;
        }

        $filePath = $this->directoryPath .'/' . $fileName;

        file_put_contents($filePath, $extractedAsset);

        return $fileName;
    }
}