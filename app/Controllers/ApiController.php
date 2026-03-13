<?php

declare(strict_types=1);

final class ApiController extends Controller
{
    public function lobby(): void
    {
        header('Content-Type: application/json; charset=UTF-8');

        $roomCode = strtoupper(trim((string) $this->input('code', '')));

        if ($roomCode === '') {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Spelkod saknas.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $roomRepository = new RoomRepository($this->app->db());
        $playerRepository = new PlayerRepository($this->app->db());

        $room = $roomRepository->findByCode($roomCode);

        if ($room === null) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Spelrum hittades inte.',
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        $players = $playerRepository->listByRoomId((int) $room['id']);

        echo json_encode([
            'success' => true,
            'room' => [
                'id' => (int) $room['id'],
                'code' => $room['room_code'],
                'status' => $room['status'],
                'current_state' => $room['current_state'],
                'match_length_minutes' => (int) $room['match_length_minutes'],
            ],
            'players' => array_map(static function (array $player): array {
                return [
                    'id' => (int) $player['id'],
                    'name' => $player['name'],
                    'age' => (int) $player['age'],
                    'avatar' => $player['avatar'],
                    'position' => (int) $player['position'],
                    'score' => (int) $player['score'],
                    'turn_order' => (int) $player['turn_order'],
                ];
            }, $players),
        ], JSON_UNESCAPED_UNICODE);
    }
}
