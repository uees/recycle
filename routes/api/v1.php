<?php

$api = app(Dingo\Api\Routing\Router::class);

$api->version('v1', [
    'namespace' => 'App\Api\V1',
    'middleware' => [
        // 'cors',
        'serializer',
        // 'serializer:array', // if you want to remove data wrap
        'api.throttle',
    ],
    // each route have a limit of 100 of 1 minutes
    'limit' => 100,
    'expires' => 1,
], function (Dingo\Api\Routing\Router $api) {
    // Auth
    // login
    $api->post('auth/login', [
        'as' => 'auth.login',
        'uses' => 'AuthController@login',
    ]);

    // User
    $api->post('users', [
        'as' => 'users.store',
        'uses' => 'UserController@store',
    ]);

    // user list
    $api->get('users', [
        'as' => 'users.index',
        'uses' => 'UserController@index',
    ]);

    // user detail
    $api->get('users/{id}', [
        'as' => 'users.show',
        'uses' => 'UserController@show',
    ]);

    $api->group(['middleware' => 'api.auth'], function (Dingo\Api\Routing\Router $api) {
        $api->put('auth/refresh', [
            'as' => 'auth.refresh',
            'uses' => 'AuthController@refresh',
        ]);

        $api->delete('auth/logout', [
            'as' => 'auth.logout',
            'uses' => 'AuthController@logout',
        ]);

        // USER
        // my detail
        $api->get('user', [
            'as' => 'user.me',
            'uses' => 'UserController@me',
        ]);

        // update part of me
        $api->patch('user', [
            'as' => 'user.update',
            'uses' => 'UserController@updateMe',
        ]);

        // update my password
        $api->put('user/password', [
            'as' => 'user.password.update',
            'uses' => 'UserController@password',
        ]);
    });
});
