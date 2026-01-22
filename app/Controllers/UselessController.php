<?php

declare(strict_types=1);

namespace CoisasInuteis\Controllers;

use CoisasInuteis\Services\UselessService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;

final class UselessController
{
    public function __construct(private readonly UselessService $service)
    {
    }

    public function fact(Request $request, Response $response): Response
    {
        return $this->json($response, ['fato' => $this->service->getRandomFact()]);
    }

    public function number(Request $request, Response $response): Response
    {
        return $this->json($response, ['numero' => $this->service->getRandomNumber()]);
    }

    public function advice(Request $request, Response $response): Response
    {
        return $this->json($response, ['conselho' => $this->service->getRandomAdvice()]);
    }

    public function contribute(Request $request, Response $response): Response
    {
        $body = (string) $request->getBody();
        $payload = json_decode($body, true);

        if (!is_array($payload)) {
            return $this->json($response->withStatus(400), ['erro' => 'JSON inválido']);
        }

        $type = isset($payload['type']) ? (string) $payload['type'] : '';
        $value = isset($payload['value']) ? (string) $payload['value'] : '';

        try {
            $this->service->contribute($type, $value);
        } catch (\InvalidArgumentException $e) {
            return $this->json($response->withStatus(422), ['erro' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return $this->json($response->withStatus(500), ['erro' => 'Falha ao salvar contribuição']);
        }

        return $this->json($response->withStatus(201), ['ok' => true]);
    }

    /** @param array<string, mixed> $data */
    private function json(Response $response, array $data): Response
    {
        $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $response->getBody()->write($payload ?: '{}');

        if (!$response->hasHeader('Content-Type')) {
            return $response->withHeader('Content-Type', 'application/json; charset=utf-8');
        }

        return $response;
    }
}
