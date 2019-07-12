<?php

defined('_WEIGHT_SPECS') or define('_WEIGHT_SPECS', [
    'g_1kg' => 1,
    'g_4kg' => 4,
    'g_5kg' => 5,
    'g_20kg' => 20,
    'x_10kg' => 10,
    'x_15kg' => 15,
    'x_18kg' => 18,
    'x_20kg' => 20,
]);

defined('_RECYCLABLE_TYPE_SPECS') or define('_RECYCLABLE_TYPE_SPECS', [
    'g_20kg' => 'bucket',
    'x_15kg' => 'bucket',
    'x_18kg' => 'bucket',
    'x_20kg' => 'box',
]);

// 获取当前登录用户
if (!function_exists('auth_user')) {
    /**
     * Get the auth_user.
     *
     * @return mixed
     */
    function auth_user()
    {
        return app('Dingo\Api\Auth\Auth')->user();
    }
}

if (!function_exists('dingo_route')) {
    /**
     * 根据别名获得url.
     *
     * @param string $version
     * @param string $name
     * @param string $params
     *
     * @return string
     */
    function dingo_route($version, $name, $params = [])
    {
        return app('Dingo\Api\Routing\UrlGenerator')
            ->version($version)
            ->route($name, $params);
    }
}

if (!function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param string $id
     * @param array $parameters
     * @param string $domain
     * @param string $locale
     *
     * @return string
     */
    function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        if (is_null($id)) {
            return app('translator');
        }

        return app('translator')
            ->trans($id, $parameters, $domain, $locale);
    }
}

if (!function_exists('make_query_condition')) {
    /**
     * @param string $filed
     * @param string $query_string
     * @return array
     */
    function make_query_condition($filed, $query_string)
    {
        $keys = array_filter(explode(' ', $query_string), function ($key) {
            return !empty($key);
        });

        return array_map(function ($key) use ($filed) {
            return [$filed, 'like', "%{$key}%"];
        }, $keys);
    }
}
