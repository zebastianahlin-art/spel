<?php
$roomCode = (string) ($room['room_code'] ?? '');
?>

<section class="game-screen" data-game-screen="host" data-room-code="<?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="grid two">
        <div class="card">
            <p class="eyebrow">TV-vy</p>
            <h1>Spelet pågår</h1>
            <div id="tv-turn-panel">
                <?php if ($latestTurn !== null): ?>
                    <div class="turn-player">
                        <div class="player-avatar xl"><?= htmlspecialchars((string) $latestTurn['player_avatar'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div>
                            <div class="player-name large"><?= htmlspecialchars((string) $latestTurn['player_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="player-meta"><?= htmlspecialchars((string) $latestTurn['category'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    </div>

                    <div class="question-box">
                        <?= htmlspecialchars((string) $latestTurn['question_text'], ENT_QUOTES, 'UTF-8'); ?>
                    </div>

                    <?php
                    $options = json_decode((string) ($latestTurn['answer_options_json'] ?? '[]'), true);
                    if (!is_array($options)) {
                        $options = [];
                    }
                    ?>
                    <div class="options-grid" id="tv-options">
                        <?php foreach ($options as $option): ?>
                            <div class="option-card"><?= htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="muted">Ingen fråga aktiv ännu.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h2>Poängtavla</h2>
            <div id="scoreboard">
                <div class="player-list">
                    <?php foreach (($players ?? []) as $player): ?>
                        <div class="player-card">
                            <div class="player-avatar"><?= htmlspecialchars($player['avatar'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div>
                                <div class="player-name"><?= htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="player-meta">Poäng: <?= (int) $player['score']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>