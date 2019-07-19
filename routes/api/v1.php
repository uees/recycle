<?php

$api = app(Dingo\Api\Routing\Router::class);

$api->version('v1', ['namespace' => 'App\Api\V1'], function (Dingo\Api\Routing\Router $api) {
    // Auth login
    $api->post('auth/login', [
        'as' => 'auth.login',
        'uses' => 'AuthController@login',
    ]);

    $api->group(['middleware' => ['api.auth']], function (Dingo\Api\Routing\Router $api) {
        $api->put('auth/refresh', [
            'as' => 'auth.refresh',
            'uses' => 'AuthController@refresh',
        ]);

        $api->delete('auth/logout', [
            'as' => 'auth.logout',
            'uses' => 'AuthController@logout',
        ]);

        $api->group(['middleware' => 'refresh_token'], function (Dingo\Api\Routing\Router $api) {
            // 用户 api
            $api->get('users', 'UserController@index');
            $api->post('users', 'UserController@store');
            $api->get('users/{id}', 'UserController@show');
            $api->patch('users/{id}', 'UserController@update');
            $api->delete('users/{id}','UserController@destroy');

            // get me info
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

            // 角色 api
            $api->get('roles', 'RoleController@index');
            $api->post('roles', 'RoleController@store');
            $api->get('roles/{id}', 'RoleController@show');
            $api->patch('roles/{id}', 'RoleController@update');
            $api->delete('roles/{id}','RoleController@destroy');

            // 入库 api
            $api->get('entering-warehouses', [
                'as' => 'entering_warehouses.index',
                'uses' => 'EnteringWarehouseController@index',
            ]);
            $api->post('entering-warehouses', [
                'as' => 'entering_warehouses.store',
                'uses' => 'EnteringWarehouseController@store',
            ]);
            $api->get('entering-warehouses/{id}', [
                'as' => 'entering_warehouses.show',
                'uses' => 'EnteringWarehouseController@show',
            ]);
            $api->patch('entering-warehouses/{id}', [
                'as' => 'entering_warehouses.update',
                'uses' => 'EnteringWarehouseController@update',
            ]);
            $api->delete('entering-warehouses/{id}', [
                'as' => 'entering_warehouses.delete',
                'uses' => 'EnteringWarehouseController@destroy',
            ]);

            // 发货 api
            $api->get('shipments', 'ShipmentController@index');
            $api->post('shipments', 'ShipmentController@store');
            $api->get('shipments/{id}', 'ShipmentController@show');
            $api->patch('shipments/{id}', 'ShipmentController@update');
            $api->delete('shipments/{id}','ShipmentController@destroy');

            // 回收 api
            $api->get('recycles', 'RecycleController@index');
            $api->get('recycles/{id}', 'RecycleController@show');
            $api->post('recycles/recycle', 'RecycleController@recycle');
            $api->patch('recycles/recycle/{id}', 'RecycleController@updateRecycled');
            $api->patch('recycles/confirm/{id}', 'RecycleController@confirm');
            $api->delete('recycles/{id}','RecycleController@destroy');

            // 检测 api
            $api->get('qc-records', 'QcRecordController@index');
            $api->post('qc-records', 'QcRecordController@store');
            $api->get('qc-records/{id}', 'QcRecordController@show');
            $api->patch('qc-records/{id}', 'QcRecordController@update');
            $api->delete('qc-records/{id}','QcRecordController@destroy');

            // 客户 api
            $api->get('customers', 'CustomerController@index');
            $api->post('customers', 'CustomerController@store');
            $api->get('customers/{id}', 'CustomerController@show');
            $api->patch('customers/{id}', 'CustomerController@update');
            $api->delete('customers/{id}','CustomerController@destroy');

            // 统计
            $api->get('recycled-statistics', 'RecycledStatisticsController@index');
            $api->get('recycled-statistics/range', 'RecycledStatisticsController@range');
            $api->post('recycled-statistics', 'RecycledStatisticsController@create');
            $api->post('recycled-statistics/customers', 'RecycledStatisticsController@createAll');
        });
    });
});
