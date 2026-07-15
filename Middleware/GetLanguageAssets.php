<?php

namespace Leantime\Plugins\Whiteboards\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Leantime\Core\Configuration\Environment;
use Leantime\Core\Http\IncomingRequest;
use Symfony\Component\HttpFoundation\Response;
use Leantime\Core\Language;

class GetLanguageAssets
{
    public function __construct(
        private Language $language,
        private Environment $config,
    ) {}

    /**
     * Load plugin language assets.
     *
     * @param \Closure(IncomingRequest): Response $next
     * @throws BindingResolutionException
     **/
    public function handle(IncomingRequest $request, Closure $next): Response
    {
        $cacheKey = 'whiteboards.languageArray';

        $languageArray = Cache::get($cacheKey, []);

        if (! empty($languageArray)) {
            $this->language->ini_array = array_merge($this->language->ini_array, $languageArray);
            return $next($request);
        }

        if (! Cache::store('installation')->has('whiteboards.language.en-US')) {
            $languageArray += parse_ini_file(__DIR__ . '/../Language/en-US.ini', true);
        }

        if (($language = $_SESSION["usersettings.language"] ?? $this->config->language) !== 'en-US') {
            if (! Cache::store('installation')->has('whiteboards.language.' . $language)) {
                Cache::store('installation')->put(
                    'whiteboards.language.' . $language,
                    parse_ini_file(__DIR__ . '/../Language/' . $language . '.ini', true)
                );
            }

            $languageArray = array_merge($languageArray, Cache::store('installation')->get('whiteboards.language.' . $language));
        }

        Cache::put($cacheKey, $languageArray);

        $this->language->ini_array = array_merge($this->language->ini_array, $languageArray);
        return $next($request);
    }
}
