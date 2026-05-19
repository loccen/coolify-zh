<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class ApplyLocale
{
    private const LOCALE_COOKIE = 'coolify_locale';

    public function handle(Request $request, Closure $next): Response
    {
        App::setLocale($this->resolveLocale($request));

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $cookieLocale = $this->normalizeLocale($request->cookie(self::LOCALE_COOKIE));
        if ($cookieLocale !== null) {
            return $cookieLocale;
        }

        foreach ($request->getLanguages() as $language) {
            $headerLocale = $this->normalizeLocale($language);

            if ($headerLocale !== null) {
                return $headerLocale;
            }
        }

        return $this->defaultLocale();
    }

    private function normalizeLocale(?string $locale): ?string
    {
        if (! is_string($locale)) {
            return null;
        }

        $normalized = str_replace('-', '_', trim($locale));
        if ($normalized === '') {
            return null;
        }

        $language = strtolower(explode('_', $normalized)[0]);

        return match (true) {
            $language === 'en' => 'en',
            $language === 'zh' => 'zh_CN',
            default => null,
        };
    }

    private function defaultLocale(): string
    {
        return $this->normalizeLocale(config('app.locale', 'zh_CN')) ?? 'zh_CN';
    }
}
