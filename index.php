<?php

require __DIR__ . '/config/config.php';

spl_autoload_register(function ($class) {
    $paths = [
        ROOT_PATH . '/core/' . $class . '.php',
        ROOT_PATH . '/controllers/' . $class . '.php',
        ROOT_PATH . '/models/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require $path;
            return;
        }
    }
});

$router = new Router();

// ----- Public site -----
$router->get('/', [HomeController::class, 'index']);

// ----- Auth -----
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// ----- Teacher dashboard -----
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->post('/dashboard/basic', [DashboardController::class, 'updateBasic']);
$router->post('/dashboard/section/{section}', [DashboardController::class, 'updateSection']);
$router->post('/dashboard/template', [DashboardController::class, 'updateTemplate']);
$router->post('/dashboard/photo', [DashboardController::class, 'uploadPhoto']);
$router->post('/dashboard/resume-upload', [DashboardController::class, 'uploadResume']);
$router->post('/dashboard/visibility', [DashboardController::class, 'visibility']);
$router->post('/dashboard/resume-access', [DashboardController::class, 'resumeAccess']);

// ----- Public portfolio -----
$router->get('/p/{slug}', [PortfolioController::class, 'show']);
$router->get('/p/{slug}/resume', [PortfolioController::class, 'resume']);
$router->post('/p/{slug}/call', [PortfolioController::class, 'logCall']);

// ----- Super admin -----
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/teachers', [AdminController::class, 'teachers']);
$router->post('/admin/teachers/{id}/status', [AdminController::class, 'updateStatus']);
$router->post('/admin/teachers/{id}/delete', [AdminController::class, 'delete']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
