<?php

namespace Webmamba\OneFileTwigComponentBundle;

class TwigComponentAssetExtractor
{
    const CSS_REGEX = '/<style.*?>(.*?)<\/style>/is';
    const JS_REGEX = '/<script.*?>(.*?)<\/script>/is';

    public function extractTwig(string $content): ?string
    {
        if (!preg_match(self::CSS_REGEX, $content) && !preg_match(self::JS_REGEX, $content)) {
            return $content;
        }

        $content = preg_replace(self::CSS_REGEX, '', $content);
        $content = preg_replace(self::JS_REGEX, '', $content);

        return $content;
    }

    public function extractCSS(string $content): ?string
    {
        return $this->extractWithPattern($content, self::CSS_REGEX);
    }

    public function extractJavaScript(string $content): ?string
    {
        return $this->extractWithPattern($content, self::JS_REGEX);
    }

    protected function extractWithPattern(string $content, string $pattern): ?string
    {
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        if (isset($matches[0][1])) {
            return $matches[0][1];
        }

        return null;
    }

    public static function checkContainJavascriptOrCss(string $content): bool
    {
        if (!preg_match(self::CSS_REGEX, $content) && !preg_match(self::JS_REGEX, $content)) {
            return false;
        }

        return true;
    }
}