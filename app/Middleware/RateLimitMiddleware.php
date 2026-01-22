<?php

declare(strict_types=1);

namespace CoisasInuteis\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

final class RateLimitMiddleware implements MiddlewareInterface
{
    private string $storePath;

    public function __construct(
        private readonly int $maxRequests,
        private readonly int $windowSeconds
    ) {
        $cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'coisas_inuteis_cache';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0700, true);
        }
        $this->storePath = $cacheDir . DIRECTORY_SEPARATOR . 'rate_limit.json';
    }

    public function process(Request $request, Handler $handler): Response
    {
        $now = time();
        $key = $this->clientKey($request);

        $store = $this->readStore();
        $entry = $store[$key] ?? ['start' => $now, 'count' => 0];

        if (!is_array($entry) || !isset($entry['start'], $entry['count'])) {
            $entry = ['start' => $now, 'count' => 0];
        }

        $start = (int) $entry['start'];
        $count = (int) $entry['count'];

        if (($now - $start) >= $this->windowSeconds) {
            $start = $now;
            $count = 0;
        }

        $count++;

        $entry = ['start' => $start, 'count' => $count];
        $store[$key] = $entry;
        $this->writeStore($store);

        if ($count > $this->maxRequests) {
            $retryAfter = max(1, ($start + $this->windowSeconds) - $now);
            $response = new SlimResponse();
            $response->getBody()->write(json_encode([
                'erro' => 'Rate limit excedido',
                'retry_after' => $retryAfter
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}');

            return $response
                ->withStatus(429)
                ->withHeader('Content-Type', 'application/json; charset=utf-8')
                ->withHeader('Retry-After', (string) $retryAfter);
        }

        return $handler->handle($request);
    }

    private function clientKey(Request $request): string
    {
        $params = $request->getServerParams();
        $ip = $params['REMOTE_ADDR'] ?? 'unknown';

        $xff = $request->getHeaderLine('X-Forwarded-For');
        if ($xff !== '') {
            $parts = array_map('trim', explode(',', $xff));
            if ($parts !== [] && $parts[0] !== '') {
                $ip = $parts[0];
            }
        }

        return 'ip:' . $ip;
    }

    /** @return array<string, mixed> */
    private function readStore(): array
    {
        if (!is_file($this->storePath)) {
            return [];
        }

        $raw = file_get_contents($this->storePath);
        if ($raw === false) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /** @param array<string, mixed> $store */
    private function writeStore(array $store): void
    {
        $json = json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            return;
        }

        file_put_contents($this->storePath, $json . "\n", LOCK_EX);
    }
}
