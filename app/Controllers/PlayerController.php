<?php

declare(strict_types=1);

final class PlayerController extends Controller
{
    public function lobby(): void
    {
        $roomCode = strtoupper(trim((string) $this->input('code', '')));

        if ($roomCode === '') {
            $this->redirect('/join');
        }

        $roomRepository = new RoomRepository($this->app->db());
        $playerRepository = new PlayerRepository($this->app->db());

        $room = $roomRepository->findByCode($roomCode);

        if ($room === null) {
            http_response_code(404);
            echo 'Spelrummet kunde inte hittas.';
            return;
        }

        $playerId = $_SESSION['player_rooms'][$roomCode] ?? null;

        if ($playerId === null) {
            http_response_code(403);
            echo 'Du har inte åtkomst till denna spelarlobby.';
            return;
        }

        $player = $playerRepository->findById((int) $playerId);

        if ($player === null || (int) $player['room_id'] !== (int) $room['id']) {
            http_response_code(403);
            echo 'Spelaren kunde inte hittas i detta rum.';
            return;
        }

        if (($room['status'] ?? 'lobby') === 'in_progress') {
            $this->redirect('/player/game?code=' . urlencode($roomCode));
        }

        $players = $playerRepository->listByRoomId((int) $room['id']);

        $this->view('player/lobby', [
            'pageTitle' => 'Väntar på spelstart',
            'room' => $room,
            'player' => $player,
            'players' => $players,
        ]);
    }

    public function game(): void
    {
        $roomCode = strtoupper(trim((string) $this->input('code', '')));

        if ($roomCode === '') {
            $this->redirect('/join');
        }

        $roomRepository = new RoomRepository($this->app->db());
        $playerRepository = new PlayerRepository($this->app->db());
        $turnRepository = new TurnRepository($this->app->db());

        $room = $roomRepository->findByCode($roomCode);

        if ($room === null) {
            http_response_code(404);
            echo 'Spelrummet kunde inte hittas.';
            return;
        }

        $playerId = $_SESSION['player_rooms'][$roomCode] ?? null;

        if ($playerId === null) {
            http_response_code(403);
            echo 'Du har inte åtkomst till denna spelvy.';
            return;
        }

        $player = $playerRepository->findById((int) $playerId);

        if ($player === null || (int) $player['room_id'] !== (int) $room['id']) {
            http_response_code(403);
            echo 'Spelaren kunde inte hittas i detta rum.';
            return;
        }

        $latestTurn = $turnRepository->getLatestByRoomId((int) $room['id']);
        $players = $playerRepository->listByRoomId((int) $room['id']);

        $this->view('player/game', [
            'pageTitle' => 'Spelet pågår',
            'room' => $room,
            'player' => $player,
            'players' => $players,
            'latestTurn' => $latestTurn,
            'submitError' => null,
        ]);
    }

    public function answer(): void
    {
        $roomCode = strtoupper(trim((string) $this->input('code', '')));
        $answer = trim((string) $this->input('answer', ''));

        if ($roomCode === '') {
            $this->redirect('/join');
        }

        $playerId = $_SESSION['player_rooms'][$roomCode] ?? null;

        if ($playerId === null) {
            http_response_code(403);
            echo 'Du har inte åtkomst till att svara i detta spel.';
            return;
        }

        if ($answer === '') {
            $this->redirect('/player/game?code=' . urlencode($roomCode));
        }

        $gameService = new GameService($this->app);
        $gameService->submitAnswer($roomCode, (int) $playerId, $answer);

        $this->redirect('/player/game?code=' . urlencode($roomCode));
    }
}