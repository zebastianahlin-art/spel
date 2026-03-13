<?php

declare(strict_types=1);

final class RoomRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(string $roomCode, string $hostSessionId, int $matchLengthMinutes): int
    {
        $sql = '
            INSERT INTO rooms (
                room_code,
                host_session_id,
                status,
                match_length_minutes,
                current_state,
                created_at,
                updated_at
            ) VALUES (
                :room_code,
                :host_session_id,
                :status,
                :match_length_minutes,
                :current_state,
                NOW(),
                NOW()
            )
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_code' => $roomCode,
            ':host_session_id' => $hostSessionId,
            ':status' => 'lobby',
            ':match_length_minutes' => $matchLengthMinutes,
            ':current_state' => 'lobby',
        ]);

        return (int) $this->database->pdo()->lastInsertId();
    }

    public function findByCode(string $roomCode): ?array
    {
        $sql = 'SELECT * FROM rooms WHERE room_code = :room_code LIMIT 1';
        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_code' => strtoupper(trim($roomCode)),
        ]);

        $room = $stmt->fetch();

        return $room !== false ? $room : null;
    }

    public function codeExists(string $roomCode): bool
    {
        return $this->findByCode($roomCode) !== null;
    }

    public function markAsStarted(int $roomId, int $currentPlayerId): void
    {
        $sql = '
            UPDATE rooms
            SET
                status = :status,
                current_state = :current_state,
                current_player_id = :current_player_id,
                started_at = NOW(),
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':status' => 'in_progress',
            ':current_state' => 'awaiting_answer',
            ':current_player_id' => $currentPlayerId,
            ':id' => $roomId,
        ]);
    }

    public function setCurrentPlayer(int $roomId, int $currentPlayerId): void
    {
        $sql = '
            UPDATE rooms
            SET
                current_player_id = :current_player_id,
                current_state = :current_state,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':current_player_id' => $currentPlayerId,
            ':current_state' => 'awaiting_answer',
            ':id' => $roomId,
        ]);
    }
}