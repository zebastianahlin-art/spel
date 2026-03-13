document.addEventListener('DOMContentLoaded', () => {
    const lobbyPage = document.querySelector('[data-lobby-page]');
    if (!lobbyPage) {
        return;
    }

    const roomCode = lobbyPage.getAttribute('data-room-code');
    if (!roomCode) {
        return;
    }

    const playersList = document.getElementById('players-list');
    const playerCount = document.getElementById('player-count');
    const isPlayerLobby = lobbyPage.getAttribute('data-lobby-page') === 'player';

    const renderPlayers = (players) => {
        if (!playersList) {
            return;
        }

        if (!Array.isArray(players) || players.length === 0) {
            playersList.innerHTML = '<p class="muted">Inga spelare anslutna ännu.</p>';
            if (playerCount) {
                playerCount.textContent = '0';
            }
            return;
        }

        const currentPlayerName = document.querySelector('.player-self-card .player-name')?.textContent?.trim() ?? '';

        const html = `
            <div class="player-list">
                ${players.map((player) => {
                    const isCurrentPlayer = isPlayerLobby && player.name === currentPlayerName;

                    return `
                        <div class="player-card${isCurrentPlayer ? ' current' : ''}">
                            <div class="player-avatar">${escapeHtml(player.avatar)}</div>
                            <div>
                                <div class="player-name">
                                    ${escapeHtml(player.name)}
                                    ${isCurrentPlayer ? '<span class="you-badge">Du</span>' : ''}
                                </div>
                                <div class="player-meta">
                                    Ålder: ${Number(player.age)} · Tur: ${Number(player.turn_order)}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;

        playersList.innerHTML = html;

        if (playerCount) {
            playerCount.textContent = String(players.length);
        }
    };

    const fetchLobbyState = async () => {
        try {
            const response = await fetch(`/api/lobby?code=${encodeURIComponent(roomCode)}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (!data || data.success !== true) {
                return;
            }

            renderPlayers(data.players || []);
        } catch (error) {
            console.error('Kunde inte hämta lobbystatus', error);
        }
    };

    fetchLobbyState();
    window.setInterval(fetchLobbyState, 2000);
});

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
