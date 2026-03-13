<section class="card narrow">
    <p class="eyebrow">Hostvy</p>
    <h1>Starta nytt spel</h1>
    <p>
        Välj matchlängd och skapa ett spelrum. Därefter får du en spelkod som spelarna använder för att ansluta.
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

    <form method="post" action="/host/create" class="form-stack">
        <div class="form-group">
            <label for="match_length_minutes">Matchlängd</label>
            <select id="match_length_minutes" name="match_length_minutes">
                <option value="10" <?= (int) (($old['match_length_minutes'] ?? 20)) === 10 ? 'selected' : ''; ?>>10 minuter</option>
                <option value="20" <?= (int) (($old['match_length_minutes'] ?? 20)) === 20 ? 'selected' : ''; ?>>20 minuter</option>
                <option value="30" <?= (int) (($old['match_length_minutes'] ?? 20)) === 30 ? 'selected' : ''; ?>>30 minuter</option>
            </select>
        </div>

        <button type="submit" class="button primary">Skapa spelrum</button>
    </form>
</section>
