<?php

return  [
    'GET' => [

        // Home
        '/' => 'HomeController@index',

        // Auth
        '/login' => 'AuthController@login',

        // Imports
        //'/imports' => 'ImportController@index',
        '/imports/upload' => 'ImportController@upload',
        '/imports/{id}' => 'ImportController@show',

        // Drivers
        '/drivers' => 'DriverController@index',
        '/drivers/{id}' => 'DriverController@show',

        // Vehicles
        '/vehicles' => 'VehicleController@index',
        '/vehicles/{id}' => 'VehicleController@show',

        // Users
        '/users' => 'UserController@index',
        '/users/{id}/show' => 'UserController@show',

    ],
    'POST' => [

        // Auth
        '/login' => 'AuthController@auth',

        // Imports
        '/imports/store' => 'ImportController@store',
        '/imports/predict/{id}' => 'ImportController@predict'

    ],
    'PUT' => [],
    'DELETE' => [],

];
