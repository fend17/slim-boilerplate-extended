<?php
if (session_status() == PHP_SESSION_NONE) {
    session_set_cookie_params(3600);
    session_start();
}

/**
 * Require the autoload script, this will automatically load our classes
 * so we don't have to require a class everytime we use a class. Evertime
 * you create a new class, remember to runt 'composer update' in the terminal
 * otherwise your classes may not be recognized.
 */
require_once '../../vendor/autoload.php';

/**
 * Here we are creating the app that will handle all the routes. We are storing
 * our database config inside of 'settings'. This config is later used inside of
 * the container inside 'App/container.php'
 */

$container = require '../App/container.php';
$app = new \Slim\App($container);
require '../App/middleware.php';


/********************************
 *          ROUTES              *
 ********************************/

// GET http://localhost:XXXX/api
$app->get('/', function ($request, $response, $args) {
    /**
     * This fetches the 'index.php'-file inside the 'views'-folder
     */
    return $this->view->render($response, 'index.php');
});

/**
 * I added basic inline login functionality. This could be extracted to a
 * separate class. If the session is set is checked in 'auth.php'
 */
$app->post('/login', function ($request, $response, $args) {
    /**
     * Everything sent in 'body' when doing a POST-request can be
     * extracted with 'getParsedBody()' from the request-object
     * https://www.slimframework.com/docs/v3/objects/request.html#the-request-body
     */
    $body = $request->getParsedBody();
    $fetchUserStatement = $this->db->prepare('SELECT * FROM users WHERE username = :username');
    $fetchUserStatement->execute([
        ':username' => $body['username']
    ]);
    $user = $fetchUserStatement->fetch();
    /**
     * If the the hash matches the password passed by the user -> create token
     */
    if (password_verify($body['password'], $user['password'])) {
        $secret = getenv("JWT_TOKEN");

        /**
         * The payload consists of the users ID so it can be decoded easily, we may want to
         * use a different claim later on: https://jwt.io/introduction/
         * Either way, we want to add a time from when the token is accepted to when it should be
         * invalidated, this is done with 'iat' and 'exp'
         */
        $payload = [
            "id" => $user['id'],
            "iat" => (new DateTime())->getTimeStamp(),
            "exp" => (new DateTime("now +2 hours"))->getTimeStamp()
        ];

        /**
         * Create the token by combining the secret, the payload and also whitelisting
         * what algorithm that should be used.
         */
        $token = \Firebase\JWT\JWT::encode($payload, $secret, "HS256");
        /**
         * And pass the token back to the yser
         */
        return $response->withJson(['token' => $token]);
    }
    return $response->withJson(['error' => 'wrong password']);
});

/**
 * Basic implementation, implement a better response
 */
$app->get('/logout', function ($request, $response, $args) {
    session_destroy();
    return $response->withJson('Success');
});


/**
 * The group is used to group everything connected to the API under '/api'
 * This was done so that we can check if the user is authed when calling '/api'
 * but we don't have to check for auth when calling '/signin'
 */
$app->group('/api', function () use ($app) {

    // GET http://localhost:XXXX/api/todos
    $app->get('/todos', function ($request, $response, $args) {
        /**
         * $this->get('Todos') is available to us because we injected it into the container
         * in 'App/container.php'. This makes it easier for us to call the database
         * inside our routes.
         */
        $allTodos = $this->get('Todos')->getAll();
        /**
         * Wrapping the data when returning as a safety thing
         * https://www.owasp.org/index.php/AJAX_Security_Cheat_Sheet#Server_Side
         */
        return $response->withJson(['data' => $allTodos]);
    });

    // GET http://localhost:XXXX/api/todos/5
    $app->get('/todos/{id}', function ($request, $response, $args) {
        /**
         * {id} is a placeholder for whatever you write after todos. So if we write
         * /todos/4 the {id} will be 4. This gets saved in the $args array
         * $args['id'] === 4
         * The name inside of '$args' must match the placeholder in the url
         * https://www.slimframework.com/docs/v3/objects/router.html#route-placeholders
         */
        $id = $args['id'];
        $singleTodo = $this->get('Todos')->getOne($id);
        return $response->withJson(['data' => $singleTodo]);
    });

    // POST http://localhost:XXXX/api/todos
    $app->post('/todos', function ($request, $response, $args) {
        /**
         * Everything sent in 'body' when doing a POST-request can be
         * extracted with 'getParsedBody()' from the request-object
         * https://www.slimframework.com/docs/v3/objects/request.html#the-request-body
         */
        $body = $request->getParsedBody();
        $newTodo = $this->get('Todos')->add($body);
        return $response->withJson(['data' => $newTodo]);
    });
});

$app->run();
