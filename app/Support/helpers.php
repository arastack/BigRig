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

if (! function_exists('img')) {
    /**
     * URL of an uploaded image resized to the given width, as WebP.
     */
    function img(?string $path, int $width = 1200): ?string
    {
        return \App\Support\Image::url($path, $width);
    }
}

if (! function_exists('brand_logo')) {
    /**
     * The site logo with its padding trimmed. Variant "light" is for light backgrounds.
     */
    function brand_logo(string $variant = 'light', int $width = 480): ?string
    {
        $settings = once(fn () => \App\Models\Settings::first());
        $path = $variant === 'dark' ? $settings?->dark_theme_logo : $settings?->light_theme_logo;

        return \App\Support\Image::url($path, $width, 90, true);
    }
}

if (! function_exists('after_response')) {
    /**
     * Run work (usually sending email) after the response has been sent, so a slow
     * or unreachable mail server never delays the page. Failures are logged, not thrown.
     */
    function after_response(callable $callback, string $description = 'Deferred task'): void
    {
        app()->terminating(function () use ($callback, $description) {
            try {
                $callback();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("{$description} failed: " . $e->getMessage());
            }
        });
    }
}

if (! function_exists('page_text')) {
    /**
     * Editable website text: the wording saved in Admin > Page text, or the
     * catalogue default from config/page-text.php. Rich (HTML) values should be
     * printed with clean_html().
     */
    function page_text(string $key, array $replace = []): string
    {
        $saved = \App\Models\PageText::values()[$key] ?? null;
        $text = filled($saved) ? $saved : (\App\Models\PageText::catalogue()[$key]['default'] ?? '');

        return $replace ? strtr($text, $replace) : $text;
    }
}
