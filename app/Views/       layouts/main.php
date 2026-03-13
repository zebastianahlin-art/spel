<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Familjespel', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a class="brand" href="/">🎉 Familjespel</a>
            <nav class="nav">
                <a href="/">Hem</a>
                <a href="/host">Starta spel</a>
                <a href="/join">Anslut</a>
            </nav>
        </div>
    </header>

    <main class="site-main">
        <div class="container">
            <?= $content ?? ''; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>Fas 1 – projektskelett</p>
        </div>
    </footer>

    <script src="/assets/js/app.js"></script>
</body>
</html>
