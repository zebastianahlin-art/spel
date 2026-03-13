<?php

declare(strict_types=1);

final class TurnRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function create(
        int $roomId,
        int $playerId,
        int $questionId,
        int $turnNumber,
        int $positionBefore
    ): int {
        $sql = '
            INSERT INTO turns (
                room_id,
                player_id,
                question_id,
                turn_number,
                state,
                answer_submitted,
                is_correct,
                dice_roll,
                position_before,
                position_after,
                score_awarded,
                created_at,
                updated_at
            ) VALUES (
                :room_id,
                :player_id,
                :question_id,
                :turn_number,
                :state,
                NULL,
                NULL,
                NULL,
                :position_before,
                :position_after,
                0,
                NOW(),
                NOW()
            )
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
            ':player_id' => $playerId,
            ':question_id' => $questionId,
            ':turn_number' => $turnNumber,
            ':state' => 'awaiting_answer',
            ':position_before' => $positionBefore,
            ':position_after' => $positionBefore,
        ]);

        return (int) $this->database->pdo()->lastInsertId();
    }

    public function getLatestByRoomId(int $roomId): ?array
    {
        $sql = '
            SELECT
                t.*,
                q.category,
                q.difficulty,
                q.question_type,
                q.question_text,
                q.correct_answer,
                q.answer_options_json,
                p.name AS player_name,
                p.avatar AS player_avatar,
                p.age AS player_age
            FROM turns t
            INNER JOIN questions q ON q.id = t.question_id
            INNER JOIN players p ON p.id = t.player_id
            WHERE t.room_id = :room_id
            ORDER BY t.id DESC
            LIMIT 1
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
        ]);

        $turn = $stmt->fetch();

        return $turn !== false ? $turn : null;
    }

    public function getNextTurnNumber(int $roomId): int
    {
        $sql = 'SELECT COALESCE(MAX(turn_number), 0) + 1 AS next_turn_number FROM turns WHERE room_id = :room_id';
        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':room_id' => $roomId,
        ]);

        $row = $stmt->fetch();

        return (int) ($row['next_turn_number'] ?? 1);
    }

    public function findById(int $turnId): ?array
    {
        $sql = 'SELECT * FROM turns WHERE id = :id LIMIT 1';
        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':id' => $turnId,
        ]);

        $turn = $stmt->fetch();

        return $turn !== false ? $turn : null;
    }

    public function resolveTurn(
        int $turnId,
        string $answerSubmitted,
        bool $isCorrect,
        int $scoreAwarded,
        ?int $diceRoll,
        int $positionAfter,
        string $state = 'answered'
    ): void {
        $sql = '
            UPDATE turns
            SET
                state = :state,
                answer_submitted = :answer_submitted,
                is_correct = :is_correct,
                dice_roll = :dice_roll,
                position_after = :position_after,
                score_awarded = :score_awarded,
                updated_at = NOW()
            WHERE id = :id
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->bindValue(':state', $state);
        $stmt->bindValue(':answer_submitted', $answerSubmitted);
        $stmt->bindValue(':is_correct', $isCorrect ? 1 : 0, PDO::PARAM_INT);

        if ($diceRoll === null) {
            $stmt->bindValue(':dice_roll', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':dice_roll', $diceRoll, PDO::PARAM_INT);
        }

        $stmt->bindValue(':position_after', $positionAfter, PDO::PARAM_INT);
        $stmt->bindValue(':score_awarded', $scoreAwarded, PDO::PARAM_INT);
        $stmt->bindValue(':id', $turnId, PDO::PARAM_INT);

        $stmt->execute();
    }
}