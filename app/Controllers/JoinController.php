<?php

declare(strict_types=1);

final class JoinController extends Controller
{
    public function index(): void
    {
        $this->view('join/index', [
            'pageTitle' => 'Anslut till spel',
        ]);
    }
}
