<section class="hero">
    <div class="card">
        <p class="eyebrow">MVP – Fas 1</p>
        <h1><?= htmlspecialchars($appName ?? 'Familjespel', ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="lead">
            Ett webbaserat familjespel där TV:n är huvudskärmen och spelarna ansluter med mobilen.
        </p>

        <div class="actions">
            <a class="button primary" href="/host">Starta nytt spel</a>
            <a class="button secondary" href="/join">Anslut till spel</a>
        </div>
    </div>

    <div class="grid two">
        <div class="card">
            <h2>Vad finns i fas 1?</h2>
            <ul>
                <li>Grundstruktur i PHP</li>
                <li>Routing</li>
                <li>Layout och sidor</li>
                <li>Databasgrund</li>
                <li>Redo för lobby i nästa steg</li>
            </ul>
        </div>

        <div class="card">
            <h2>Nästa steg</h2>
            <ul>
                <li>Skapa spelrum</li>
                <li>Generera spelkod</li>
                <li>Spelare ansluter</li>
                <li>Namn, ålder och avatar</li>
                <li>Lobby med uppdatering</li>
            </ul>
        </div>
    </div>
</section>
