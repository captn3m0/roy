<?php
require 'vendor/autoload.php';
require 'app/config.php';
require 'app/models.php';
require 'app/controllers.php';
require 'app/views.php';

$routes = array(
    '/' => 'IndexController',
    '/item' => 'ItemController',
    '/{a}' => 'TeamController',
    '/{a}/calendar' => 'TeamCalendarController',
    '/{a}/settings' => 'SettingsController',
    '/oauth' => 'OAuthController',
    '/oauth/callback' => 'CallbackController'
);

Link::all($routes);