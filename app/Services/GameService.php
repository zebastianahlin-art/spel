<?php

declare(strict_types=1);

final class GameService
{
    private RoomRepository $roomRepository;
    private PlayerRepository $playerRepository;
    private QuestionRepository $questionRepository;
    private TurnRepository $turnRepository;

    public function __construct(App $app)
    {
        $database = $app->db();

        $this->roomRepository = new RoomRepository($database);
        $this->playerRepository = new PlayerRepository($database);
        $this->questionRepository = new QuestionRepository($database);
        $this->turnRepository = new TurnRepository($database);
    }

    public function startGame(string $roomCode): bool
    {
        $room = $this->roomRepository->findByCode($roomCode);

        if ($room === null) {
            return false;
        }

        $roomId = (int) $room['id'];
        $players = $this->playerRepository->listByRoomId($roomId);

        if (count($players) < 2) {
            return false;
        }

        $firstPlayer = $players[0];
        $question = $this->questionRepository->getRandomApprovedMultipleChoiceForAge((int) $firstPlayer['age']);

        if ($question === null) {
            return false;
        }

        $this->roomRepository->markAsStarted($roomId, (int) $firstPlayer['id']);
        $this->createTurnForPlayer($roomId, $firstPlayer, $question);

        return true;
    }

    public function submitAnswer(string $roomCode, int $playerId, string $answer): bool
    {
        $room = $this->roomRepository->findByCode($roomCode);

        if ($room === null) {
            return false;
        }

        $roomId = (int) $room['id'];
        $latestTurn = $this->turnRepository->getLatestByRoomId($roomId);

        if ($latestTurn === null) {
            return false;
        }

        if ((int) $latestTurn['player_id'] !== $playerId) {
            return false;
        }

        if (($latestTurn['state'] ?? '') !== 'awaiting_answer') {
            return false;
        }

        $correctAnswer = trim((string) ($latestTurn['correct_answer'] ?? ''));
        $submittedAnswer = trim($answer);

        $isCorrect = mb_strtolower($submittedAnswer) === mb_strtolower($correctAnswer);
        $scoreAwarded = $isCorrect ? 10 : 0;

        $this->turnRepository->resolveTurn(
            (int) $latestTurn['id'],
            $submittedAnswer,
            $isCorrect,
            $scoreAwarded
        );

        if ($isCorrect) {
            $this->playerRepository->addScore((int) $latestTurn['player_id'], $scoreAwarded);
        }

        $nextPlayer = $this->getNextPlayer($roomId, (int) $latestTurn['player_id']);

        if ($nextPlayer !== null) {
            $question = $this->questionRepository->getRandomApprovedMultipleChoiceForAge((int) $nextPlayer['age']);

            if ($question !== null) {
                $this->roomRepository->setCurrentPlayer($roomId, (int) $nextPlayer['id']);
                $this->createTurnForPlayer($roomId, $nextPlayer, $question);
            }
        }

        return true;
    }

    private function createTurnForPlayer(int $roomId, array $player, array $question): void
    {
        $turnNumber = $this->turnRepository->getNextTurnNumber($roomId);

        $this->turnRepository->create(
            $roomId,
            (int) $player['id'],
            (int) $question['id'],
            $turnNumber,
            (int) $player['position']
        );
    }

    private function getNextPlayer(int $roomId, int $currentPlayerId): ?array
    {
        $players = $this->playerRepository->listByRoomId($roomId);

        if (empty($players)) {
            return null;
        }

        $playerCount = count($players);
        $currentIndex = 0;

        foreach ($players as $index => $player) {
            if ((int) $player['id'] === $currentPlayerId) {
                $currentIndex = $index;
                break;
            }
        }

        $nextIndex = ($currentIndex + 1) % $playerCount;

        return $players[$nextIndex] ?? null;
    }
}