<?php

declare(strict_types=1);

final class GameController extends Controller
{
    public function show(): void
    {
        $roomCode = strtoupper(trim((string) $this->input('code', '')));

        if ($roomCode === '') {
            $this->redirect('/host');
        }

        $roomRepository = new RoomRepository($this->app->db());
        $playerRepository = new PlayerRepository($this->app->db());
        $turnRepository = new TurnRepository($this->app->db());

        $room = $roomRepository->findByCode($roomCode);

        if ($room === null) {
            http_response_code(404);
            echo 'Spelrum kunde inte hittas.';
            return;
        }

        $ownedRoomId = $_SESSION['host_rooms'][$roomCode] ?? null;

        if ((int) $ownedRoomId !== (int) $room['id']) {
            http_response_code(403);
            echo 'Du har inte åtkomst till denna spelvy.';
            return;
        }

        $players = $playerRepository->listByRoomId((int) $room['id']);
        $latestTurn = $turnRepository->getLatestByRoomId((int) $room['id']);

        $this->view('game/show', [
            'pageTitle' => 'Spelet är igång',
            'room' => $room,
            'players' => $players,
            'latestTurn' => $latestTurn,
        ]);
    }
}