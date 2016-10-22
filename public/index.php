<?php
/**
 * Created by PhpStorm.
 * User: tveitan
 * Date: 2016-10-22
 * Time: 12:25
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

/*
 * Configuration should later be set in config file and environment variables from build server.
 */
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

$config['db']['host']   = "localhost";
$config['db']['user']   = "loopen";
$config['db']['pass']   = "secret";
$config['db']['dbname'] = "test";

/*
 * Initialize the slim app. http://www.slimframework.com/docs/tutorial/first-app.html
 */
$app = new \Slim\App(["settings" => $config]);


/*
 * Get dependency injection container
 */
$container = $app->getContainer();

/*
 * Inject logger into app
 */
$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('CreativeLoop_API');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

/*
 * Inject PDO connection to mysql into app.
 */
$container['db'] = function($c) {
    $db = $c['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

/*
 * Define routes - should be moved to separate class later.
 */
$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");

    $this->logger->addInfo("Got request for $name...");

    return $response;
});

$app->get('/test/db', function (Request $request, Response $response) {
    $dbTest = new \CreativeLoop\TestDB($this->db);
    $test = $dbTest->testGet();

    $this->logger->addInfo(sprintf("Got from DB: %s", print_r($test)));

    return $test;

});

/*
 * Run the app.
 */
$app->run();
