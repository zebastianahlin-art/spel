<?php

declare(strict_types=1);

final class HostController extends Controller
{
    public function index(): void
    {
        $this->view('host/index', [
            'pageTitle' => 'Starta spel',
        ]);
    }
}
