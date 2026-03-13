<section class="card narrow">
    <p class="eyebrow">Spelarvy</p>
    <h1>Anslut till spel</h1>
    <p>
        Ange spelkod, ditt namn, din ålder och välj en avatar.
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

    <form method="post" action="/join" class="form-stack">
        <div class="form-group">
            <label for="room_code">Spelkod</label>
            <input
                type="text"
                id="room_code"
                name="room_code"
                maxlength="10"
                value="<?= htmlspecialchars((string) ($old['room_code'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Exempel: A7K9Q"
                required
            >
        </div>

        <div class="form-group">
            <label for="name">Namn</label>
            <input
                type="text"
                id="name"
                name="name"
                maxlength="100"
                value="<?= htmlspecialchars((string) ($old['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Ditt namn"
                required
            >
        </div>

        <div class="form-group">
            <label for="age">Ålder</label>
            <input
                type="number"
                id="age"
                name="age"
                min="1"
                max="120"
                value="<?= htmlspecialchars((string) ($old['age'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Till exempel 12"
                required
            >
        </div>

        <div class="form-group">
            <label for="avatar">Avatar</label>
            <select id="avatar" name="avatar" required>
                <?php foreach (($avatars ?? []) as $avatarValue => $avatarLabel): ?>
                    <option
                        value="<?= htmlspecialchars($avatarValue, ENT_QUOTES, 'UTF-8'); ?>"
                        <?= (($old['avatar'] ?? 'robot') === $avatarValue) ? 'selected' : ''; ?>
                    >
                        <?= htmlspecialchars($avatarLabel, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="button primary">Anslut till spel</button>
    </form>
</section>
