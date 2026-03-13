document.addEventListener('DOMContentLoaded', () => {
    initLobbyPage();
    initGameScreen();
});

function initLobbyPage() {
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

        playersList.innerHTML = `
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
                                    Ålder: ${Number(player.age)} · Tur: ${Number(player.turn_order)} · Poäng: ${Number(player.score)}
                                </div>
                            </div>
                        </div>
                    `;
                }).join('')}
            </div>
        `;

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

            if (data.room?.status === 'in_progress') {
                window.location.href = isPlayerLobby
                    ? `/player/game?code=${encodeURIComponent(roomCode)}`
                    : `/game?code=${encodeURIComponent(roomCode)}`;
            }
        } catch (error) {
            console.error('Kunde inte hämta lobbystatus', error);
        }
    };

    fetchLobbyState();
    window.setInterval(fetchLobbyState, 2000);
}

function initGameScreen() {
    const gameScreen = document.querySelector('[data-game-screen]');
    if (!gameScreen) {
        return;
    }

    const roomCode = gameScreen.getAttribute('data-room-code');
    const screenType = gameScreen.getAttribute('data-game-screen');
    const currentPlayerId = Number(gameScreen.getAttribute('data-player-id') || 0);

    const fetchGameState = async () => {
        try {
            const response = await fetch(`/api/game-state?code=${encodeURIComponent(roomCode)}`, {
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

            renderScoreboard(data.players || [], data.board?.max_position || 30);
            renderBoardPlayers(data.players || [], data.board?.max_position || 30);

            if (screenType === 'host') {
                renderHostTurn(data.turn);
                renderRevealBox(data.turn);
            }

            if (screenType === 'player') {
                const activePlayerId = Number(data.room?.current_player_id || 0);
                const shouldBeOnPlayerGame = data.room?.status === 'in_progress';

                if (!shouldBeOnPlayerGame) {
                    window.location.href = `/player/lobby?code=${encodeURIComponent(roomCode)}`;
                    return;
                }

                if (activePlayerId !== currentPlayerId) {
                    window.location.reload();
                    return;
                }
            }
        } catch (error) {
            console.error('Kunde inte hämta spelstatus', error);
        }
    };

    fetchGameState();
    window.setInterval(fetchGameState, 3000);
}

function renderScoreboard(players, maxPosition) {
    const scoreboard = document.getElementById('scoreboard');
    if (!scoreboard || !Array.isArray(players)) {
        return;
    }

    scoreboard.innerHTML = `
        <div class="player-list">
            ${players.map((player) => `
                <div class="player-card">
                    <div class="player-avatar">${escapeHtml(player.avatar)}</div>
                    <div>
                        <div class="player-name">${escapeHtml(player.name)}</div>
                        <div class="player-meta">Poäng: ${Number(player.score)} · Position: ${Number(player.position)}/${Number(maxPosition)}</div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

function renderBoardPlayers(players, maxPosition) {
    const boardPlayers = document.getElementById('board-players');
    if (!boardPlayers || !Array.isArray(players)) {
        return;
    }

    boardPlayers.innerHTML = players.map((player) => `
        <div class="board-player-chip">
            <span class="chip-avatar">${escapeHtml(player.avatar)}</span>
            <span class="chip-name">${escapeHtml(player.name)}</span>
            <span class="chip-pos">${Number(player.position)}/${Number(maxPosition)}</span>
        </div>
    `).join('');
}

function renderHostTurn(turn) {
    const panel = document.getElementById('tv-turn-panel');
    if (!panel || !turn) {
        return;
    }

    const options = Array.isArray(turn.answer_options) ? turn.answer_options : [];

    panel.innerHTML = `
        <div class="turn-player">
            <div class="player-avatar xl">${escapeHtml(turn.player_avatar || '')}</div>
            <div>
                <div class="player-name large">${escapeHtml(turn.player_name || '')}</div>
                <div class="player-meta">${escapeHtml(turn.category || '')}</div>
            </div>
        </div>

        <div class="question-box">
            ${escapeHtml(turn.question_text || '')}
        </div>

        <div class="options-grid">
            ${options.map((option) => `<div class="option-card">${escapeHtml(option)}</div>`).join('')}
        </div>
    `;
}

function renderRevealBox(turn) {
    const revealBox = document.getElementById('answer-reveal-box');
    if (!revealBox || !turn || turn.is_correct === null || typeof turn.is_correct === 'undefined') {
        return;
    }

    revealBox.innerHTML = `
        <div class="reveal ${Number(turn.is_correct) === 1 ? 'correct' : 'wrong'}">
            <div class="reveal-title">${Number(turn.is_correct) === 1 ? 'Rätt svar!' : 'Fel svar'}</div>
            <div class="reveal-line">Spelarens svar: ${escapeHtml(turn.answer_submitted || '')}</div>
            <div class="reveal-line">Rätt svar: ${escapeHtml(turn.correct_answer || '')}</div>
            <div class="reveal-line">Poäng: ${Number(turn.score_awarded || 0)}</div>
            <div class="reveal-line">Tärning: ${turn.dice_roll === null ? '–' : Number(turn.dice_roll)}</div>
            <div class="reveal-line">Flytt: ${Number(turn.position_before || 0)} → ${Number(turn.position_after || 0)}</div>
        </div>
    `;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}