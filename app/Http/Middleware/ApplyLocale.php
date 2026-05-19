<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyLocale
{
    private const LOCALE_COOKIE = 'coolify_locale';

    /**
     * @var array<int, string>
     */
    private const SUPPORTED_LOCALES = ['en', 'zh-cn'];

    /**
     * @var array<string, string>
     */
    private array $translationCache = [];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        $response = $next($request);

        if ($locale === 'en') {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if (! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (! is_string($content) || $content === '') {
            return $response;
        }

        $response->setContent($this->translateHtml($content));

        return $response;
    }

    private function resolveLocale(Request $request): string
    {
        $preferredLanguage = $request->headers->has('Accept-Language')
            ? $request->getPreferredLanguage(self::SUPPORTED_LOCALES)
            : null;

        $preferred = $request->cookie(self::LOCALE_COOKIE)
            ?? $request->query('locale')
            ?? $preferredLanguage
            ?? config('app.locale', 'zh-cn');

        return $this->normalizeLocale((string) $preferred);
    }

    private function normalizeLocale(string $locale): string
    {
        $normalized = strtolower(str_replace('_', '-', trim($locale)));

        return match (true) {
            str_starts_with($normalized, 'zh') => 'zh-cn',
            str_starts_with($normalized, 'en') => 'en',
            in_array($normalized, self::SUPPORTED_LOCALES, true) => $normalized,
            default => 'zh-cn',
        };
    }

    private function translateHtml(string $html): string
    {
        [$maskedHtml, $placeholders] = $this->maskNonRenderableBlocks($html);

        $translated = preg_replace_callback('/>([^<]*[A-Za-z][^<]*)</u', function (array $matches): string {
            return '>'.$this->translateTextNode($matches[1]).'<';
        }, $maskedHtml) ?? $maskedHtml;

        $translated = preg_replace_callback('/\b(title|placeholder|aria-label|alt)=("|\')(.*?)\2/u', function (array $matches): string {
            $translatedAttribute = $this->translateNormalizedValue($matches[3]);

            return sprintf('%s=%s%s%s', $matches[1], $matches[2], $translatedAttribute, $matches[2]);
        }, $translated) ?? $translated;

        $translated = preg_replace_callback('/\b(:title|x-bind:title|:aria-label|x-bind:aria-label)=("|\')(.*?)\2/u', function (array $matches): string {
            $value = preg_replace_callback('/([\'"])([^\'"]*[A-Za-z][^\'"]*)\1/u', function (array $stringMatches): string {
                $translatedString = $this->translateNormalizedValue($stringMatches[2]);

                return $stringMatches[1].$translatedString.$stringMatches[1];
            }, $matches[3]) ?? $matches[3];

            return sprintf('%s=%s%s%s', $matches[1], $matches[2], $value, $matches[2]);
        }, $translated) ?? $translated;

        return strtr($translated, $placeholders);
    }

    /**
     * @return array{0: string, 1: array<string, string>}
     */
    private function maskNonRenderableBlocks(string $html): array
    {
        $placeholders = [];
        $index = 0;

        $masked = preg_replace_callback('/<(script|style|pre|code|textarea)\b[^>]*>.*?<\/\1>/is', function (array $matches) use (&$placeholders, &$index): string {
            $placeholder = sprintf('__COOLIFY_I18N_BLOCK_%d__', $index++);
            $placeholders[$placeholder] = $matches[0];

            return $placeholder;
        }, $html) ?? $html;

        return [$masked, $placeholders];
    }

    private function translateTextNode(string $text): string
    {
        $leadingWhitespace = '';
        $trailingWhitespace = '';

        if (preg_match('/^\s+/u', $text, $matches) === 1) {
            $leadingWhitespace = $matches[0];
        }
        if (preg_match('/\s+$/u', $text, $matches) === 1) {
            $trailingWhitespace = $matches[0];
        }

        $translated = $this->translateNormalizedValue($text);

        return $leadingWhitespace.$translated.$trailingWhitespace;
    }

    private function translateNormalizedValue(string $value): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        if (! is_string($normalized) || $normalized === '' || ! preg_match('/[A-Za-z]/u', $normalized)) {
            return $value;
        }

        if (array_key_exists($normalized, $this->translationCache)) {
            return $this->translationCache[$normalized];
        }

        $translated = __($normalized);
        $this->translationCache[$normalized] = $translated;

        return $translated;
    }
}
