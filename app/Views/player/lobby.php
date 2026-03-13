<?php
$roomCode = (string) ($room['room_code'] ?? '');
$currentPlayerId = (int) ($player['id'] ?? 0);
?>

<section
    class="lobby-page"
    data-lobby-page="player"
    data-room-code="<?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>"
>
    <div class="grid two lobby-grid">
        <div class="card">
            <p class="eyebrow">Du är ansluten</p>
            <h1>Väntar på spelstart</h1>
            <p class="lead">
                Du är med i spelrum <strong><?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?></strong>.
                Vänta tills värden startar spelet på TV-skärmen.
            </p>

            <div class="player-self-card">
                <div class="player-avatar large"><?= htmlspecialchars((string) ($player['avatar'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                <div>
                    <div class="player-name"><?= htmlspecialchars((string) ($player['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="player-meta">Ålder: <?= (int) ($player['age'] ?? 0); ?></div>
                </div>
            </div>

            <div class="placeholder-box">
                <strong>Nästa fas:</strong><br>
                Spelstart, första frågan och aktiv spelarvy.
            </div>
        </div>

        <div class="card">
            <div class="section-header">
                <h2>Spelare i lobbyn</h2>
            </div>

            <div id="players-list">
                <div class="player-list">
                    <?php foreach (($players ?? []) as $listedPlayer): ?>
                        <div class="player-card<?= (int) $listedPlayer['id'] === $currentPlayerId ? ' current' : ''; ?>">
                            <div class="player-avatar"><?= htmlspecialchars($listedPlayer['avatar'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div>
                                <div class="player-name">
                                    <?= htmlspecialchars($listedPlayer['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    <?php if ((int) $listedPlayer['id'] === $currentPlayerId): ?>
                                        <span class="you-badge">Du</span>
                                    <?php endif; ?>
                                </div>
                                <div class="player-meta">Ålder: <?= (int) $listedPlayer['age']; ?> · Tur: <?= (int) $listedPlayer['turn_order']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
