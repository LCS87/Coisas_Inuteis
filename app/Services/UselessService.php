<?php

declare(strict_types=1);

namespace CoisasInuteis\Services;

final class UselessService
{
    private string $dataDir;

    public function __construct(string $dataDir)
    {
        $this->dataDir = rtrim($dataDir, DIRECTORY_SEPARATOR);
    }

    public function getRandomFact(): string
    {
        return $this->randomFromFile('facts.json');
    }

    public function getRandomNumber(): int
    {
        $value = $this->randomFromFile('numbers.json');

        if (is_numeric($value)) {
            return (int) $value;
        }

        $asInt = filter_var($value, FILTER_VALIDATE_INT);
        if ($asInt !== false) {
            return (int) $asInt;
        }

        return random_int(0, 999);
    }

    public function getRandomAdvice(): string
    {
        return $this->randomFromFile('advices.json');
    }

    public function contribute(string $type, string $value): void
    {
        $type = trim($type);
        $value = trim($value);

        if ($value === '') {
            throw new \InvalidArgumentException('value não pode ser vazio');
        }

        $file = match ($type) {
            'fato' => 'facts.json',
            'numero' => 'numbers.json',
            'conselho' => 'advices.json',
            default => null
        };

        if ($file === null) {
            throw new \InvalidArgumentException('type inválido (use fato|numero|conselho)');
        }

        $path = $this->dataPath($file);
        $items = $this->readJsonArray($path);

        $items[] = $type === 'numero' ? $this->normalizeNumber($value) : $value;

        $this->writeJsonArray($path, $items);
    }

    private function normalizeNumber(string $value): int
    {
        $value = trim($value);
        $int = filter_var($value, FILTER_VALIDATE_INT);
        if ($int === false) {
            throw new \InvalidArgumentException('value deve ser um inteiro para type=numero');
        }
        return (int) $int;
    }

    private function randomFromFile(string $file): string
    {
        $items = $this->readJsonArray($this->dataPath($file));

        if ($items === []) {
            return 'Nada extremamente inútil disponível no momento.';
        }

        $idx = array_rand($items);
        $value = $items[$idx];

        return is_string($value) ? $value : (string) $value;
    }

    private function dataPath(string $file): string
    {
        return $this->dataDir . DIRECTORY_SEPARATOR . $file;
    }

    /** @return array<int, mixed> */
    private function readJsonArray(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values($decoded);
    }

    /** @param array<int, mixed> $items */
    private function writeJsonArray(string $path, array $items): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $json = json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new \RuntimeException('Falha ao serializar JSON');
        }

        $fp = fopen($path, 'c+');
        if ($fp === false) {
            throw new \RuntimeException('Falha ao abrir arquivo de dados');
        }

        try {
            if (!flock($fp, LOCK_EX)) {
                throw new \RuntimeException('Falha ao travar arquivo de dados');
            }

            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, $json . "\n");
            fflush($fp);
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }
}
