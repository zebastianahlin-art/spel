<?php
$roomCode = (string) ($room['room_code'] ?? '');
$playerCount = is_array($players ?? null) ? count($players) : 0;
?>

<section
    class="lobby-page"
    data-lobby-page="host"
    data-room-code="<?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>"
>
    <div class="grid two lobby-grid">
        <div class="card">
            <p class="eyebrow">Host-lobby</p>
            <h1>Spelrum <?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead">
                Låt spelarna gå till <strong>/join</strong> och ange spelkoden.
            </p>

            <?php if (!empty($errors ?? [])): ?>
                <div class="alert error">
                    <ul>
                        <?php foreach (($errors ?? []) as $error): ?>
                            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="room-code-box" id="room-code-box">
                <?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>
            </div>

            <div class="lobby-meta">
                <div class="meta-pill">
                    Matchlängd: <?= (int) ($room['match_length_minutes'] ?? 20); ?> min
                </div>
                <div class="meta-pill">
                    Spelare: <span id="player-count"><?= $playerCount; ?></span>/8
                </div>
            </div>

            <form method="post" action="/host/start" class="form-stack">
                <input type="hidden" name="code" value="<?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="button primary">Starta spel</button>
            </form>
        </div>

        <div class="card">
            <div class="section-header">
                <h2>Anslutna spelare</h2>
            </div>

            <div id="players-list">
                <?php if ($playerCount === 0): ?>
                    <p class="muted">Inga spelare anslutna ännu.</p>
                <?php else: ?>
                    <div class="player-list">
                        <?php foreach ($players as $player): ?>
                            <div class="player-card">
                                <div class="player-avatar"><?= htmlspecialchars($player['avatar'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div>
                                    <div class="player-name"><?= htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="player-meta">Ålder: <?= (int) $player['age']; ?> · Tur: <?= (int) $player['turn_order']; ?> · Poäng: <?= (int) $player['score']; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>