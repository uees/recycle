<?php

$api = app(Dingo\Api\Routing\Router::class);

$api->version('v1', [
    'namespace' => 'App\Api\V1',
    'middleware' => [
        'cors',
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
    $api->post('authorizations', [
        'as' => 'authorizations.store',
        'uses' => 'AuthController@store',
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


});
