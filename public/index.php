<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/app/Core/App.php';
require_once BASE_PATH . '/app/Core/Router.php';
require_once BASE_PATH . '/app/Core/Controller.php';
require_once BASE_PATH . '/app/Core/Database.php';
require_once BASE_PATH . '/app/Core/View.php';

require_once BASE_PATH . '/app/Repositories/RoomRepository.php';
require_once BASE_PATH . '/app/Repositories/PlayerRepository.php';
require_once BASE_PATH . '/app/Repositories/QuestionRepository.php';
require_once BASE_PATH . '/app/Repositories/TurnRepository.php';

require_once BASE_PATH . '/app/Services/GameService.php';

require_once BASE_PATH . '/app/Controllers/HomeController.php';
require_once BASE_PATH . '/app/Controllers/HostController.php';
require_once BASE_PATH . '/app/Controllers/JoinController.php';
require_once BASE_PATH . '/app/Controllers/PlayerController.php';
require_once BASE_PATH . '/app/Controllers/GameController.php';
require_once BASE_PATH . '/app/Controllers/ApiController.php';

$config = [
    'app' => require BASE_PATH . '/config/app.php',
    'database' => require BASE_PATH . '/config/database.php',
];

$app = new App($config);

$router = $app->router();

$router->get('/', ['HomeController', 'index']);

$router->get('/host', ['HostController', 'index']);
$router->post('/host/create', ['HostController', 'create']);
$router->get('/host/lobby', ['HostController', 'lobby']);
$router->post('/host/start', ['HostController', 'start']);

$router->get('/join', ['JoinController', 'index']);
$router->post('/join', ['JoinController', 'join']);

$router->get('/player/lobby', ['PlayerController', 'lobby']);
$router->get('/player/game', ['PlayerController', 'game']);
$router->post('/player/answer', ['PlayerController', 'answer']);

$router->get('/game', ['GameController', 'show']);

$router->get('/api/lobby', ['ApiController', 'lobby']);
$router->get('/api/game-state', ['ApiController', 'gameState']);

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');