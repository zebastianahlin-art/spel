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

    public function gameState(): void
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
        $turnRepository = new TurnRepository($this->app->db());

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
        $latestTurn = $turnRepository->getLatestByRoomId((int) $room['id']);

        $questionOptions = [];
        if (!empty($latestTurn['answer_options_json'])) {
            $decoded = json_decode((string) $latestTurn['answer_options_json'], true);
            if (is_array($decoded)) {
                $questionOptions = $decoded;
            }
        }

        echo json_encode([
            'success' => true,
            'room' => [
                'id' => (int) $room['id'],
                'code' => $room['room_code'],
                'status' => $room['status'],
                'current_state' => $room['current_state'],
                'current_player_id' => $room['current_player_id'] !== null ? (int) $room['current_player_id'] : null,
            ],
            'players' => array_map(static function (array $player): array {
                return [
                    'id' => (int) $player['id'],
                    'name' => $player['name'],
                    'age' => (int) $player['age'],
                    'avatar' => $player['avatar'],
                    'score' => (int) $player['score'],
                    'turn_order' => (int) $player['turn_order'],
                ];
            }, $players),
            'turn' => $latestTurn ? [
                'id' => (int) $latestTurn['id'],
                'player_id' => (int) $latestTurn['player_id'],
                'player_name' => $latestTurn['player_name'],
                'player_avatar' => $latestTurn['player_avatar'],
                'question_text' => $latestTurn['question_text'],
                'category' => $latestTurn['category'],
                'state' => $latestTurn['state'],
                'is_correct' => $latestTurn['is_correct'] !== null ? (int) $latestTurn['is_correct'] : null,
                'answer_submitted' => $latestTurn['answer_submitted'],
                'correct_answer' => $latestTurn['correct_answer'],
                'answer_options' => $questionOptions,
                'score_awarded' => (int) $latestTurn['score_awarded'],
            ] : null,
        ], JSON_UNESCAPED_UNICODE);
    }
}