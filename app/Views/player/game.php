<?php
$roomCode = (string) ($room['room_code'] ?? '');
$currentPlayerId = (int) ($player['id'] ?? 0);
$activePlayerId = (int) ($room['current_player_id'] ?? 0);
$isMyTurn = $currentPlayerId === $activePlayerId;
$options = [];

if (!empty($latestTurn['answer_options_json'])) {
    $decoded = json_decode((string) $latestTurn['answer_options_json'], true);
    if (is_array($decoded)) {
        $options = $decoded;
    }
}
?>

<section class="game-screen" data-game-screen="player" data-room-code="<?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>" data-player-id="<?= $currentPlayerId; ?>">
    <div class="grid two">
        <div class="card">
            <p class="eyebrow">Mobilvy</p>
            <h1><?= $isMyTurn ? 'Det är din tur' : 'Vänta på din tur'; ?></h1>

            <div id="player-turn-panel">
                <?php if ($latestTurn !== null): ?>
                    <div class="player-self-card">
                        <div class="player-avatar large"><?= htmlspecialchars((string) ($player['avatar'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                        <div>
                            <div class="player-name"><?= htmlspecialchars((string) ($player['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="player-meta">Poäng: <?= (int) ($player['score'] ?? 0); ?></div>
                        </div>
                    </div>

                    <?php if ($isMyTurn && (($latestTurn['state'] ?? '') === 'awaiting_answer')): ?>
                        <div class="question-box mobile">
                            <?= htmlspecialchars((string) $latestTurn['question_text'], ENT_QUOTES, 'UTF-8'); ?>
                        </div>

                        <form method="post" action="/player/answer" class="form-stack">
                            <input type="hidden" name="code" value="<?= htmlspecialchars($roomCode, ENT_QUOTES, 'UTF-8'); ?>">

                            <?php foreach ($options as $index => $option): ?>
                                <label class="radio-option">
                                    <input type="radio" name="answer" value="<?= htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8'); ?>" required>
                                    <span><?= htmlspecialchars((string) $option, ENT_QUOTES, 'UTF-8'); ?></span>
                                </label>
                            <?php endforeach; ?>

                            <button type="submit" class="button primary">Skicka svar</button>
                        </form>
                    <?php else: ?>
                        <div class="placeholder-box">
                            Aktiv spelare: <strong><?= htmlspecialchars((string) ($latestTurn['player_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong><br>
                            Vänta tills det blir din tur.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="muted">Väntar på första frågan.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h2>Poängtavla</h2>
            <div id="scoreboard">
                <div class="player-list">
                    <?php foreach (($players ?? []) as $listedPlayer): ?>
                        <div class="player-card<?= (int) $listedPlayer['id'] === $currentPlayerId ? ' current' : ''; ?>">
                            <div class="player-avatar"><?= htmlspecialchars($listedPlayer['avatar'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div>
                                <div class="player-name"><?= htmlspecialchars($listedPlayer['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="player-meta">Poäng: <?= (int) $listedPlayer['score']; ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>