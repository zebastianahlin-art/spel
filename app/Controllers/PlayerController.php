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

        $players = $playerRepository->listByRoomId((int) $room['id']);

        $this->view('player/lobby', [
            'pageTitle' => 'Väntar på spelstart',
            'room' => $room,
            'player' => $player,
            'players' => $players,
        ]);
    }
}
