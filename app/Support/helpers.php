<?php

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

if (! function_exists('clean_html')) {
    /**
     * Strip scripts, event handlers and unsafe URLs from rich text (CKEditor output)
     * while keeping ordinary formatting, links, lists, tables and images.
     */
    function clean_html(?string $html): string
    {
        static $sanitizer;

        $sanitizer ??= new HtmlSanitizer(
            (new HtmlSanitizerConfig())
                ->allowSafeElements()
                ->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
                ->allowMediaSchemes(['https', 'http', 'data'])
                ->allowRelativeLinks()
                ->allowRelativeMedias()
                ->forceAttribute('a', 'rel', 'noopener noreferrer')
                ->withMaxInputLength(500000)
        );

        return $sanitizer->sanitize((string) $html);
    }
}
