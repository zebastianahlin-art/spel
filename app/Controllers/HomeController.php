<?php

declare(strict_types=1);

final class HomeController extends Controller
{
    public function index(): void
    {
        $appConfig = $this->app->config('app');

        $this->view('home/index', [
            'pageTitle' => 'Familjespel',
            'appName' => $appConfig['name'] ?? 'Familjespel',
        ]);
    }
}
