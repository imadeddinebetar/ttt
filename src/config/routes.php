<?php

return [
    'GET' => [

        // Home
        '/home/{id}' => 'HomeController@index',

        // Imports
        '/imports' => 'ImportController@index',
        '/imports/create' => 'ImportController@create',
        '/imports/{id}' => 'ImportController@show',

        // Drivers
        '/drivers' => 'DriverController@index',
        '/drivers/{id}' => 'DriverController@show',

        // Vehicles
        '/vehicles' => 'VehicleController@index',
        '/vehicles/{id}' => 'VehicleController@show',

        // Users
        '/users' => 'UserController@index',
        '/users/{id}' => 'UserController@show',

    ],
    'POST' => [

        // Imports
        '/imports/upload' => 'ImportController@upload'

    ],
    'PUT' => [],
    'DELETE' => [],

];
