<?php

use App\Models\LobbyCode;
use Livewire\Livewire;

test('renders each code with its reward and requirement', function () {
    LobbyCode::factory()->create([
        'code' => 'MagicIsReal',
        'reward' => '5,000 Sprite Dust',
        'requirement' => "Complete Bastian's Story Quest first",
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSeeLivewire('pages::lobby-codes')
        ->assertSee('MagicIsReal')
        ->assertSee('5,000 Sprite Dust')
        ->assertSee("Requires: Complete Bastian's Story Quest first");
});

test('lists newer codes first and expired codes last', function () {
    $expiredCode = LobbyCode::factory()->expired()->create();
    $olderCode = LobbyCode::factory()->create();
    $newerCode = LobbyCode::factory()->create();

    Livewire::test('pages::lobby-codes')
        ->assertSeeInOrder([$newerCode->code, $olderCode->code, $expiredCode->code]);
});

test('checking a code marks it as redeemed', function () {
    $lobbyCode = LobbyCode::factory()->create();

    Livewire::test('pages::lobby-codes')
        ->call('setRedeemed', $lobbyCode->id, true);

    expect($lobbyCode->fresh()->redeemed_at)->not->toBeNull();
});

test('unchecking a code clears its redemption', function () {
    $lobbyCode = LobbyCode::factory()->redeemed()->create();

    Livewire::test('pages::lobby-codes')
        ->call('setRedeemed', $lobbyCode->id, false);

    expect($lobbyCode->fresh()->redeemed_at)->toBeNull();
});

test('checking an already redeemed code keeps the original redemption time', function () {
    $lobbyCode = LobbyCode::factory()->create(['redeemed_at' => now()->subDay()]);
    $originalRedeemedAt = $lobbyCode->redeemed_at;

    Livewire::test('pages::lobby-codes')
        ->call('setRedeemed', $lobbyCode->id, true);

    expect($lobbyCode->fresh()->redeemed_at->equalTo($originalRedeemedAt))->toBeTrue();
});

test('the to redeem filter hides redeemed and expired codes', function () {
    $unredeemedCode = LobbyCode::factory()->create();
    $redeemedCode = LobbyCode::factory()->redeemed()->create();
    $expiredCode = LobbyCode::factory()->expired()->create();

    Livewire::test('pages::lobby-codes')
        ->set('filter', 'unused')
        ->assertSee($unredeemedCode->code)
        ->assertDontSee($redeemedCode->code)
        ->assertDontSee($expiredCode->code);
});

test('the redeemed filter shows only redeemed codes', function () {
    $unredeemedCode = LobbyCode::factory()->create();
    $redeemedCode = LobbyCode::factory()->redeemed()->create();

    Livewire::test('pages::lobby-codes')
        ->set('filter', 'used')
        ->assertSee($redeemedCode->code)
        ->assertDontSee($unredeemedCode->code);
});

test('progress counts only codes that have not expired', function () {
    LobbyCode::factory()->redeemed()->create();
    LobbyCode::factory()->create();
    LobbyCode::factory()->expired()->redeemed()->create();

    Livewire::test('pages::lobby-codes')
        ->assertSee('1 of 2 redeemed');
});
