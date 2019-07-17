<?php

if (!function_exists('recyclable_type')) {
    function recyclable_type($product_name)
    {
        if (preg_match("/^A-9060[ABCDEFG].*内袋$/i", $product_name)) {
            return 'bucket';
        } elseif (preg_match("/^SP.*内袋$/i", $product_name)) {
            return 'bucket';
        } elseif (preg_match("/.*内袋$/i", $product_name)) {
            return 'box';
        }
        return '';
    }
}

if (!function_exists('calc_amount')) {
    function calc_amount($weight, $spec, $recyclable_type)
    {
        $matches = null;
        $per_weight = 0;
        $amount = 0;
        preg_match('/\d+\.?\d+/', $spec, $matches);
        if (!empty($matches)) {
            $per_weight = (float)$matches[0];
            
            if ($recyclable_type == 'box') {
                if ($per_weight < 10) {
                    $per_weight = 10;
                } elseif ($per_weight < 20) {
                    $per_weight = 10;
                }
            } elseif ($recyclable_type == 'bucket') {
                if ($per_weight < 20) {
                    $per_weight = 10;
                }
            }
        }

        if ($per_weight != 0) {
            $amount = (int)($weight / $per_weight);
        }

        return $amount;
    }
}

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
