<?php

declare(strict_types=1);

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        $layoutFile = BASE_PATH . '/app/Views/' . $layout . '.php';

        if (!file_exists($viewFile)) {
            throw new RuntimeException('View saknas: ' . $viewFile);
        }

        if (!file_exists($layoutFile)) {
            throw new RuntimeException('Layout saknas: ' . $layoutFile);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }
}
