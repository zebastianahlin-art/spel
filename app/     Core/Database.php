<?php

declare(strict_types=1);

use PDO;
use PDOException;

final class Database
{
    private ?PDO $pdo = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function pdo(): PDO
    {
        if ($this->pdo !== null) {
            return $this->pdo;
        }

        $driver = $this->config['driver'] ?? 'mysql';

        if ($driver !== 'mysql') {
            throw new RuntimeException('Endast mysql stöds i fas 1.');
        }

        $host = $this->config['host'] ?? '127.0.0.1';
        $port = $this->config['port'] ?? 3306;
        $dbname = $this->config['database'] ?? '';
        $charset = $this->config['charset'] ?? 'utf8mb4';
        $username = $this->config['username'] ?? '';
        $password = $this->config['password'] ?? '';

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host,
            $port,
            $dbname,
            $charset
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Databasanslutning misslyckades: ' . $e->getMessage());
        }

        return $this->pdo;
    }

    public function testConnection(): bool
    {
        $this->pdo()->query('SELECT 1');
        return true;
    }
}
