<?php

declare(strict_types=1);

use CoisasInuteis\Controllers\UselessController;
use CoisasInuteis\Middleware\RateLimitMiddleware;
use CoisasInuteis\Services\UselessService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response as SlimResponse;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();

$app->add(function (Request $request, $handler): Response {
    $response = $handler->handle($request);

    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
});

$app->options('/{routes:.+}', function (Request $request, Response $response): Response {
    return $response;
});

$service = new UselessService(__DIR__ . '/../app/Data');
$controller = new UselessController($service);

$app->get('/fato', [$controller, 'fact']);
$app->get('/numero', [$controller, 'number']);
$app->get('/conselho', [$controller, 'advice']);

$app->post('/contribuir', [$controller, 'contribute'])
    ->add(new RateLimitMiddleware(10, 60));

$app->get('/', function (Request $request, Response $response): Response {
    $payload = json_encode([
        'nome' => 'Coisas Inúteis',
        'endpoints' => [
            'GET /fato',
            'GET /numero',
            'GET /conselho',
            'POST /contribuir'
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $response->getBody()->write($payload ?: '{}');

    return $response;
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler(function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app): Response {
    $response = new SlimResponse();
    $payload = json_encode([
        'erro' => 'Erro interno',
        'mensagem' => $displayErrorDetails ? $exception->getMessage() : 'Tente novamente mais tarde'
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $response->getBody()->write($payload ?: '{}');

    return $response->withStatus(500)->withHeader('Content-Type', 'application/json; charset=utf-8');
});

$app->run();
