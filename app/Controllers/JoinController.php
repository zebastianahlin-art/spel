<?php

declare(strict_types=1);

final class JoinController extends Controller
{
    public function index(): void
    {
        $this->view('join/index', [
            'pageTitle' => 'Anslut till spel',
            'errors' => [],
            'old' => [
                'room_code' => '',
                'name' => '',
                'age' => '',
                'avatar' => 'robot',
            ],
            'avatars' => $this->avatars(),
        ]);
    }

    public function join(): void
    {
        $roomCode = strtoupper(trim((string) $this->input('room_code', '')));
        $name = trim((string) $this->input('name', ''));
        $age = (int) $this->input('age', 0);
        $avatar = trim((string) $this->input('avatar', ''));

        $errors = [];

        if ($roomCode === '' || strlen($roomCode) < 4) {
            $errors[] = 'Ange en giltig spelkod.';
        }

        if ($name === '' || mb_strlen($name) > 100) {
            $errors[] = 'Ange ett namn mellan 1 och 100 tecken.';
        }

        if ($age < 1 || $age > 120) {
            $errors[] = 'Ange en giltig ålder mellan 1 och 120.';
        }

        if (!array_key_exists($avatar, $this->avatars())) {
            $errors[] = 'Välj en giltig avatar.';
        }

        $roomRepository = new RoomRepository($this->app->db());
        $playerRepository = new PlayerRepository($this->app->db());

        $room = $roomRepository->findByCode($roomCode);

        if ($room === null) {
            $errors[] = 'Spelrummet kunde inte hittas.';
        } elseif (($room['status'] ?? 'lobby') !== 'lobby') {
            $errors[] = 'Spelet är inte öppet för anslutning.';
        }

        if (!empty($errors)) {
            $this->view('join/index', [
                'pageTitle' => 'Anslut till spel',
                'errors' => $errors,
                'old' => [
                    'room_code' => $roomCode,
                    'name' => $name,
                    'age' => $age > 0 ? (string) $age : '',
                    'avatar' => $avatar !== '' ? $avatar : 'robot',
                ],
                'avatars' => $this->avatars(),
            ]);
            return;
        }

        $roomId = (int) $room['id'];
        $turnOrder = $playerRepository->getNextTurnOrder($roomId);
        $playerId = $playerRepository->create($roomId, $name, $age, $avatar, $turnOrder);

        $_SESSION['player_rooms'] ??= [];
        $_SESSION['player_rooms'][$roomCode] = $playerId;

        $this->redirect('/player/lobby?code=' . urlencode($roomCode));
    }

    private function avatars(): array
    {
        return [
            'robot' => '🤖 Robot',
            'cat' => '🐱 Katt',
            'pizza' => '🍕 Pizza',
            'dino' => '🦖 Dinosaurie',
            'viking' => '🛡️ Viking',
            'rocket' => '🚀 Raket',
            'ghost' => '👻 Spöke',
            'banana' => '🍌 Banan',
        ];
    }
}
