<?php

declare(strict_types=1);

abstract class Controller
{
    protected App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    protected function view(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        View::render($view, $data, $layout);
    }
}
