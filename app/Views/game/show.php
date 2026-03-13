<?php
$roomCode = (string) ($room['room_code'] ?? '');
$boardMax = 30;
?>

<section class="game-screen" data-game-screen="host" data-room-code="<?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>">
    <div class="grid two">
        <div class="card">
            <p class="eyebrow">TV-vy</p>
            <h1>Spelet pågår</h1>

            <div class="board-panel">
                <div class="board-header">
                    <div class="city-label">Västerås</div>
                    <div class="city-label">Uppsala</div>
                </div>

                <div class="board-track" id="board-track">
                    <?php for ($i = 0; $i <= $boardMax; $i++): ?>
                        <div class="board-step">
                            <span><?= $i; ?></span>
                        </div>
                    <?php endfor; ?>
                </div>

                <div id="board-players" class="board-players">
                    <?php foreach (($players ?? []) as $player): ?>
                        <div class="board-player-chip">
                            <span class="chip-avatar"><?= htmlspecialchars($player['avatar'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="chip-name"><?= htmlspecialchars($player['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="chip-pos"><?= (int) $player['position']; ?>/<?= $boardMax; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

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
                                <div class="player-meta">Poäng: <?= (int) $player['score']; ?> · Position: <?= (int) $player['position']; ?>/<?= $boardMax; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="answer-reveal-box" class="answer-reveal-box">
                <?php if ($latestTurn !== null && ($latestTurn['is_correct'] ?? null) !== null): ?>
                    <div class="reveal <?= (int) $latestTurn['is_correct'] === 1 ? 'correct' : 'wrong'; ?>">
                        <div class="reveal-title">
                            <?= (int) $latestTurn['is_correct'] === 1 ? 'Rätt svar!' : 'Fel svar'; ?>
                        </div>
                        <div class="reveal-line">Spelarens svar: <?= htmlspecialchars((string) ($latestTurn['answer_submitted'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="reveal-line">Rätt svar: <?= htmlspecialchars((string) ($latestTurn['correct_answer'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="reveal-line">Poäng: <?= (int) ($latestTurn['score_awarded'] ?? 0); ?></div>
                        <div class="reveal-line">
                            Tärning:
                            <?= $latestTurn['dice_roll'] !== null ? (int) $latestTurn['dice_roll'] : '–'; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>