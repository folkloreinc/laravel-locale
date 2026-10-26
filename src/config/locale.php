<?php

return [
    'locales' => ['en', 'fr'],

    'uses_user_locale_preference' => true,

    'request_parameter' => null,

    // When no locale is detected, use app.locale if it is in `locales`, instead of the first
    // entry of `locales`. Off by default in 8.x to keep the previous behaviour.
    'fallback_to_app_locale' => false,

    'detect_from_route' => true,

    'detect_from_headers' => true,

    'store_in_session' => true,

    'share_with_views' => true,

    'set_carbon_locale' => true,

    'translations_namespace' => 'routes',
];
