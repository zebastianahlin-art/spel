<?php

declare(strict_types=1);

final class HostController extends Controller
{
    public function index(): void
    {
        $this->view('host/index', [
            'pageTitle' => 'Starta spel',
            'errors' => [],
            'old' => [
                'match_length_minutes' => 20,
            ],
        ]);
    }

    public function create(): void
    {
        $matchLengthMinutes = (int) $this->input('match_length_minutes', 20);

        $allowedLengths = [10, 20, 30];
        $errors = [];

        if (!in_array($matchLengthMinutes, $allowedLengths, true)) {
            $errors[] = 'Ogiltig matchlängd.';
        }

        if (!empty($errors)) {
            $this->view('host/index', [
                'pageTitle' => 'Starta spel',
                'errors' => $errors,
                'old' => [
                    'match_length_minutes' => $matchLengthMinutes,
                ],
            ]);
            return;
        }

        $roomRepository = new RoomRepository($this->app->db());

        $roomCode = $this->generateUniqueRoomCode($roomRepository);
        $hostSessionId = session_id();

        $roomId = $roomRepository->create($roomCode, $hostSessionId, $matchLengthMinutes);

        $_SESSION['host_rooms'] ??= [];
        $_SESSION['host_rooms'][$roomCode] = $roomId;

        $this->redirect('/host/lobby?code=' . urlencode($roomCode));
    }

    public function lobby(): void
    {
        $roomCode = strtoupper(trim((string) $this->input('code', '')));

        if ($roomCode === '') {
            $this->redirect('/host');
        }

        $roomRepository = new RoomRepository($this->app->db());
        $playerRepository = new PlayerRepository($this->app->db());

        $room = $roomRepository->findByCode($roomCode);

        if ($room === null) {
            http_response_code(404);
            echo 'Spelrum kunde inte hittas.';
            return;
        }

        $ownedRoomId = $_SESSION['host_rooms'][$roomCode] ?? null;

        if ((int) $ownedRoomId !== (int) $room['id']) {
            http_response_code(403);
            echo 'Du har inte åtkomst till denna host-lobby.';
            return;
        }

        if (($room['status'] ?? 'lobby') === 'in_progress') {
            $this->redirect('/game?code=' . urlencode($roomCode));
        }

        $players = $playerRepository->listByRoomId((int) $room['id']);

        $this->view('host/lobby', [
            'pageTitle' => 'Lobby ' . $roomCode,
            'room' => $room,
            'players' => $players,
            'errors' => [],
        ]);
    }

    public function start(): void
    {
        $roomCode = strtoupper(trim((string) $this->input('code', '')));

        if ($roomCode === '') {
            $this->redirect('/host');
        }

        $roomRepository = new RoomRepository($this->app->db());
        $playerRepository = new PlayerRepository($this->app->db());

        $room = $roomRepository->findByCode($roomCode);

        if ($room === null) {
            http_response_code(404);
            echo 'Spelrum kunde inte hittas.';
            return;
        }

        $ownedRoomId = $_SESSION['host_rooms'][$roomCode] ?? null;

        if ((int) $ownedRoomId !== (int) $room['id']) {
            http_response_code(403);
            echo 'Du har inte åtkomst till detta spelrum.';
            return;
        }

        $players = $playerRepository->listByRoomId((int) $room['id']);
        $errors = [];

        if (count($players) < 2) {
            $errors[] = 'Minst 2 spelare måste vara anslutna för att starta spelet.';
        }

        $gameService = new GameService($this->app);
        if (empty($errors) && !$gameService->startGame($roomCode)) {
            $errors[] = 'Spelet kunde inte startas. Kontrollera att det finns godkända frågor i databasen.';
        }

        if (!empty($errors)) {
            $this->view('host/lobby', [
                'pageTitle' => 'Lobby ' . $roomCode,
                'room' => $room,
                'players' => $players,
                'errors' => $errors,
            ]);
            return;
        }

        $this->redirect('/game?code=' . urlencode($roomCode));
    }

    private function generateUniqueRoomCode(RoomRepository $roomRepository): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maxIndex = strlen($characters) - 1;

        do {
            $code = '';

            for ($i = 0; $i < 5; $i++) {
                $code .= $characters[random_int(0, $maxIndex)];
            }
        } while ($roomRepository->codeExists($code));

        return $code;
    }
}