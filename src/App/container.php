<?php

/**
 * We rename or make a shortcut to the TodoController
 */
use \App\Controllers\TodoController as TodoController;

require_once 'ConfigHandler.php';

/**
 * Return a new config with all the database credentials. If you need to change
 * the connection to the database, do it inside of ConfigHandler and change your credentials
 * in the function 'getDefaultConfig()'
 */
$config = (new ConfigHandler())->getConfig();

/**
 * The container is responsible for 'injecting' all our dependecies.
 * If we want to use some class or database when using our routes
 * we can inject it here. WE must first get the container from our $app.
 * $app is the new Slim(); we declared in `index.php`
 */
$container = new \Slim\Container(['settings' => $config]);

/**
 * The container is an associative array of different dependecies that
 * our $app needs. Below we are storing our database connection inside
 * of the app. This will result in that we can call our database
 * in our routes like: $this->get('db'). The config is sent in
 * when we are creating our new Slim App in index.php.
 */
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO(
      'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=utf8',
      $db['user'],
      $db['pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // We must always return what we want to inject
    return $pdo;
};

/**
 * We are also injecting a logging function so we can easier spot
 * and log errors that occur.
 */
$container['logger'] = function ($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

/**
 * This is so we can display a frontpage. Otherwise Slim is only designed
 * to be an API framework so we have to extend it a bit.
 */
$container['view'] = function ($container) {
    return new \Slim\Views\PhpRenderer('../public/views/');
};

/**
 * This is how you would inject your own class. You defined what it will be called
 * as $container['your_name']. Inside here we are creating a new TodoController
 * that will handle adding and removing todos. The TodoController itself
 * needs a database so we inject the database when we create the controller.
 * $c always refers to the whole container. So calling $c->get('db') is the same
 * as calling $this->get('db') in our routes in index.php
 */

$container['todos'] = function ($c) {
    $todosController = new TodoController($c->get('db'));
    return $todosController;
};

$container['users'] = function ($c) {
    $userControllers = new \App\Controllers\UserController($c->get('db'));
    return $userControllers;
};

return $container;
