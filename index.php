<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';

$baseDirec = str_replace(basename($_SERVER['SCRIPT_NAME']), '',$_SERVER['SCRIPT_NAME']);
$baseUrl = 'http://' . $_SERVER['HTTP_HOST'] . $baseDirec;
define('BASE_URL', $baseUrl);
//var_dump($baseDirec);
//var_dump($baseUrl);
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);//El parámeto que recibe es dónde se encuentra el .env
$dotenv->load();//Cargar el archivo
//echo (__DIR__ . '\..');die;
//echo getenv('DB_USER');die;

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),//Variables de entorno
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$route = $_GET['route'] ?? '/';

// function render($filename, $params = []){
//     ob_start();
//     extract($params);
//     include $filename;

//     return ob_get_clean();
// }
use Phroute\Phroute\RouteCollector;

$router = new RouteCollector();

$router->controller('/admin', App\Controllers\Admin\IndexController::class);

$router->controller('/admin/posts', App\Controllers\Admin\PostController::class);

$router->controller('/', App\Controllers\IndexController::class);
    
    // $query = $pdo->prepare('SELECT * FROM blog_posts ORDER BY id DESC');
    // $query->execute();
    // $blogP = $query->fetchAll(PDO::FETCH_ASSOC);

    // return render('../views/index.php', ['blogP' => $blogP]); 

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());

$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $route);

echo $response;
