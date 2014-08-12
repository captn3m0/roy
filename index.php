<?php
require 'vendor/autoload.php';
require 'app/models.php';
require 'app/controllers.php';
require 'app/views.php';

$routes = array(
    '/' => 'IndexController',
    '/{a}' => 'TeamController',
    '/{a}/calendar' => 'TeamCalendarController',
    '/{a}/settings' => 'SettingsController',
    '/item' => 'ItemController'
);

Link::all($routes);