<?php

declare(strict_types=1);

namespace CoisasInuteis\Tests;

use CoisasInuteis\Services\UselessService;
use PHPUnit\Framework\TestCase;

final class UselessServiceTest extends TestCase
{
    public function testRandomFactReturnsString(): void
    {
        $dir = $this->makeTempDataDir([
            'facts.json' => json_encode(['a', 'b'], JSON_UNESCAPED_UNICODE),
            'numbers.json' => json_encode([1, 2], JSON_UNESCAPED_UNICODE),
            'advices.json' => json_encode(['x', 'y'], JSON_UNESCAPED_UNICODE),
        ]);

        $service = new UselessService($dir);
        $value = $service->getRandomFact();

        $this->assertIsString($value);
        $this->assertNotSame('', $value);
    }

    public function testContributeNumberPersistsAsInt(): void
    {
        $dir = $this->makeTempDataDir([
            'facts.json' => json_encode([], JSON_UNESCAPED_UNICODE),
            'numbers.json' => json_encode([1], JSON_UNESCAPED_UNICODE),
            'advices.json' => json_encode([], JSON_UNESCAPED_UNICODE),
        ]);

        $service = new UselessService($dir);
        $service->contribute('numero', '123');

        $raw = file_get_contents($dir . DIRECTORY_SEPARATOR . 'numbers.json');
        $decoded = json_decode($raw ?: '[]', true);

        $this->assertIsArray($decoded);
        $this->assertContains(123, $decoded);
    }

    /** @param array<string, string> $files */
    private function makeTempDataDir(array $files): string
    {
        $base = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'coisas_inuteis_test_' . bin2hex(random_bytes(4));
        mkdir($base, 0777, true);

        foreach ($files as $name => $content) {
            file_put_contents($base . DIRECTORY_SEPARATOR . $name, $content . "\n");
        }

        return $base;
    }
}
