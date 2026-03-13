<?php

declare(strict_types=1);

final class App
{
    private array $config;
    private Router $router;
    private ?Database $database = null;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->router = new Router($this);
    }

    public function config(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function db(): Database
    {
        if ($this->database === null) {
            $this->database = new Database($this->config('database'));
        }

        return $this->database;
    }
}
