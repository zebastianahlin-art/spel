<?php

declare(strict_types=1);

final class QuestionRepository
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function getRandomApprovedMultipleChoiceForAge(int $age): ?array
    {
        $sql = '
            SELECT *
            FROM questions
            WHERE is_approved = 1
              AND question_type = :question_type
              AND age_min <= :age
              AND age_max >= :age
            ORDER BY RAND()
            LIMIT 1
        ';

        $stmt = $this->database->pdo()->prepare($sql);
        $stmt->execute([
            ':question_type' => 'multiple_choice',
            ':age' => $age,
        ]);

        $question = $stmt->fetch();

        if ($question !== false) {
            return $question;
        }

        $fallbackSql = '
            SELECT *
            FROM questions
            WHERE is_approved = 1
              AND question_type = :question_type
            ORDER BY RAND()
            LIMIT 1
        ';

        $fallbackStmt = $this->database->pdo()->prepare($fallbackSql);
        $fallbackStmt->execute([
            ':question_type' => 'multiple_choice',
        ]);

        $fallback = $fallbackStmt->fetch();

        return $fallback !== false ? $fallback : null;
    }
}