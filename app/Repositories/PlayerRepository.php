<?php

declare(strict_types=1);

final class PlayerRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(int $roomId, string $name, int $age, string $avatar, int $turnOrder): int
    {
        $sql = '
            INSERT INTO players (
                room_id,
                name,
                age,
                avatar,
                position,
                score,
                is_connected,
                turn_order,
                is_finalist,
                created_at,
                updated_at
            ) VALUES (
                :room_id,
                :name,
                :age,
                :avatar,
                0,
                0,
                1,
                :turn_order,
                0,
                NOW(),
                NOW()
            )
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
            ':name' => $name,
            ':age' => $age,
            ':avatar' => $avatar,
            ':turn_order' => $turnOrder,
        ]);

        return (int) $this->database->pdo()->lastInsertId();
    }

    public function getNextTurnOrder(int $roomId): int
    {
        $sql = 'SELECT COALESCE(MAX(turn_order), 0) + 1 AS next_turn_order FROM players WHERE room_id = :room_id';
        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
        ]);

        $row = $stmt->fetch();

        return (int) ($row['next_turn_order'] ?? 1);
    }

    public function findById(int $playerId): ?array
    {
        $sql = 'SELECT * FROM players WHERE id = :id LIMIT 1';
        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':id' => $playerId,
        ]);

        $player = $stmt->fetch();

        return $player !== false ? $player : null;
    }

    public function listByRoomId(int $roomId): array
    {
        $sql = '
            SELECT
                id,
                room_id,
                name,
                age,
                avatar,
                position,
                score,
                is_connected,
                turn_order
            FROM players
            WHERE room_id = :room_id
            ORDER BY turn_order ASC, id ASC
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
        ]);

        return $stmt->fetchAll() ?: [];
    }

    public function countByRoomId(int $roomId): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM players WHERE room_id = :room_id';
        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
        ]);

        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    }

    public function addScore(int $playerId, int $score): void
    {
        $sql = '
            UPDATE players
            SET
                score = score + :score,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':score' => $score,
            ':id' => $playerId,
        ]);
    }
}