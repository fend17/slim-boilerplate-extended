# Slim Boilerplate

> Boilerplate for [Slim Framework](https://www.slimframework.com/docs/)

## Installation

0. Install `PHP` so you can use it via your terminal: [Installation Instructions](https://github.com/fend17/cms-php-mysql/blob/master/installation.md#sv%C3%A5ra-s%C3%A4ttetd)
1. Install **[`Composer`](https://getcomposer.org/doc/00-intro.md)**.
2. Make sure you can run composer by writing `composer --version` in the terminal of your choice. If it's working you should get back a version number. If you get something like `command not found` -> restart computer -> try again -> revisit installation steps in the link above.
3. Clone this repository: `git clone` somwhere on your computer, doesn't matter where.
4. `cd` into the cloned repository.
3. Run `composer install` (or `php composer.phar install` depending how you installed composer) from the terminal to install all dependencies that the project needs.
4. Open up `src/ConfigHandler` and find the function `getDefaultConfig`. Make sure your credentials to your database are correct. Remember to have MAMP running if you are connecting through MySQL on MAMP.

## Usage

* From the terminal, run the following:
```php
php -S localhost:3000 -t src/public
```
* then visit [`http://localhost:3000`](http://localhost:3000)

## Structure

* All routes are defined inside of `src/public/index.php`. This is your main entry point for adding new functionality to the API
* All controllers are put inside of `src/App/Controllers` and the filename must be the same as the classname. Remember to run `composer update` when adding a new class
```php
<?php
// CatController.php
namespace App\Controllers;

class CatController
{
}
```
* The frontpage can be edited by changing `src/public/views/index.php`
* To inject your own controller into the app you must edit `src/App/container.php`
```php
use \App\Controllers\CatController as CatController;
// $this->get('Cats') inside of routes
$container['Cats'] = function ($c) {
    $todosController = new CatController($c->get('db'));
    return $todosController;
};
```

# Deploying

1. Create an account on [Heroku](https://heroku.com/) and remember your username and password, they will be needed in a later step.
2. Install [Heroku toolbelt](https://devcenter.heroku.com/articles/heroku-cli), follow the instructions at the link.
3. Login via your terminal by running: `heroku login`.
4. Make sure you are standing in the root folder of the project, check with `pwd` in the terminal.
4. Run: `heroku create my-cool-project-name` and replace `my-cool-project-name` with the name of your choice.
5. Run: `heroku addons:create cleardb:ignite --fork=mysql://root:root@localhost/todos` and exchange your credentials (username: `root`, password: `root`) and the database that you want to export (`todos` is just an example).
6. Run: `heroku open` to go to your created site.