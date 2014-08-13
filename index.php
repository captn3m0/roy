<?php
require 'vendor/autoload.php';
require 'app/config.php';
require 'app/models.php';
require 'app/controllers.php';
require 'app/views.php';

$routes = array(
    '/' => 'IndexController',
    '/item' => 'ItemController',
    '/item/{a}' => 'ItemDoneController',
    '/{a}' => 'TeamController',
    '/{a}/calendar' => 'TeamCalendarController',
    '/{a}/settings' => 'SettingsController',
    '/{a}/users/update' => 'UpdateUsersController',
    '/{a}/channels/update' => 'UpdateChannelsController',
    '/oauth' => 'OAuthController',
    '/oauth/callback' => 'CallbackController',
    '/session' => 'SessionController'
);
Link::before(function(){
  session_start();
});
Link::all($routes);