<?php

use App\Models\LobbyCode;
use Database\Seeders\LobbyCodeSeeder;

test('reseeding refreshes existing codes without duplicating them or clearing redemptions', function () {
    $this->seed(LobbyCodeSeeder::class);
    $seededCodeCount = LobbyCode::count();

    $lobbyCode = LobbyCode::where('code', 'MagicIsReal')->firstOrFail();
    $lobbyCode->update(['reward' => 'Outdated reward', 'redeemed_at' => now()]);

    $this->seed(LobbyCodeSeeder::class);

    $lobbyCode->refresh();

    expect(LobbyCode::count())->toBe($seededCodeCount)
        ->and($lobbyCode->reward)->toBe('5,000 Sprite Dust')
        ->and($lobbyCode->redeemed_at)->not->toBeNull();
});
