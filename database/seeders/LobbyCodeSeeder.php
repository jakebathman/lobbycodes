<?php

namespace Database\Seeders;

use App\Models\LobbyCode;
use Illuminate\Database\Seeder;

class LobbyCodeSeeder extends Seeder
{
    /**
     * Admin Panel lobby codes, newest first. Add new codes to the top of the list.
     *
     * Source: https://www.ign.com/wikis/fortnite/All_Admin_Panel_Lobby_Hack_Codes_For_Free_Rewards
     *
     * @var list<array{code: string, reward: string, requirement?: string, is_expired?: bool}>
     */
    private const array LOBBY_CODES = [
        ['code' => 'PumpkinSpiceLife', 'reward' => 'Transforms you into a pumpkin temporarily'],
        ['code' => 'CrowsAreAfraid', 'reward' => 'Transforms you into a scarecrow temporarily'],
        ['code' => 'IThinkTheKeyFoundMeChat', 'reward' => '1x Extraction Accelerator'],
        ['code' => 'POWEROUT', 'reward' => "Five Nights at Freddy's lobby jumpscare"],
        ['code' => 'Bonerattler', 'reward' => '4x Spicy Taco'],
        ['code' => 'WhoCrackedTheCode', 'reward' => '40,000 XP'],
        ['code' => 'WeAreTheWorldChampionsToday', 'reward' => 'FNCS Sentry Back Bling'],
        ['code' => 'DustySprites', 'reward' => '5,000 Sprite Dust'],
        ['code' => 'AlmostScaringSeason', 'reward' => '2x Cheat Code Locator'],
        ['code' => '9YEARS', 'reward' => '9th Birthday Sprite Spray'],
        ['code' => 'NOCTURNEOP55N1', 'reward' => '2x Extraction Accelerator'],
        ['code' => 'BLINKYINKYPINKYCLYDE', 'reward' => '5,000 Sprite Dust'],
        ['code' => 'DestinyAwaits', 'reward' => '2x Llama Supply Drop'],
        ['code' => 'ChatFindMeAnotherCode', 'reward' => '2x Cheat Code Locator'],
        ['code' => 'MagicIsReal', 'reward' => '5,000 Sprite Dust', 'requirement' => "Complete Bastian's Story Quest first"],
        ['code' => 'PlayToLevelUp', 'reward' => '2,000 Sprite Dust'],
        ['code' => 'SAYH12WR1X3L', 'reward' => "Wrixel's Hero Portrait Spray"],
        ['code' => 'BEAMMEUP', 'reward' => '2x Extraction Accelerator'],
        ['code' => 'DustInTheWind', 'reward' => '5,000 Sprite Dust'],
        ['code' => 'WhereIsTheDustyTree', 'reward' => '5,000 Sprite Dust'],
        ['code' => 'BRB', 'reward' => 'Transforms you into a toilet temporarily'],
        ['code' => 'InsertCoinToContinue', 'reward' => 'Transforms you into an arcade machine temporarily'],
        ['code' => 'ChatWhereDoYouFindTheKey', 'reward' => '2x Extraction Accelerator'],
        ['code' => 'INVALIDCHEAT', 'reward' => '2x Cheat Code Locator'],
        ['code' => 'YourThoughtsAreMine', 'reward' => '5,000 Sprite Dust, Void Master Geno Skin Edit Style, Void Conduits of Power Back Bling Edit Style', 'requirement' => "Complete Geno's Story quests first"],
        ['code' => 'JONESYISGOLDEN', 'reward' => 'Gold Jonesy Sprite'],
        ['code' => 'GatherAndCraft', 'reward' => 'Cheat Master Bush Sprite', 'requirement' => "Complete Wrixel (Ziggy)'s Story quests first"],
        ['code' => 'Play4All', 'reward' => 'Cheat Master Jonesy Sprite'],
        ['code' => 'GottaGoFast', 'reward' => 'Cheat Master Sonic Sprite'],
        ['code' => 'IWannaFlyHigh', 'reward' => 'Cheat Master Tails Sprite'],
        ['code' => '8BitBlast', 'reward' => 'Cheat Master 8-Bit Sprite'],
        ['code' => 'BORN2PLAY', 'reward' => 'Cheat Master Adventure Sprite'],
        ['code' => 'OverrideXP', 'reward' => '40,000 XP'],
        ['code' => 'O2OVERRIDE', 'reward' => '1x Llama Supply Drop, 5x Portable Extractor'],
        ['code' => 'TakeYourHeart', 'reward' => '2x Extraction Accelerator'],
        ['code' => 'SurviveTheNight', 'reward' => '2x Cheat Code Locator'],
        ['code' => 'FindItChat', 'reward' => '2x Cheat Code Locator'],
        ['code' => 'PerfectOrder', 'reward' => '4x Spicy Taco'],
        ['code' => 'H0p0nVC', 'reward' => '2,000 Sprite Dust'],
        ['code' => 'Magilume', 'reward' => '2,000 Sprite Dust'],
        ['code' => 'Chispambo', 'reward' => '2,000 Sprite Dust'],
        ['code' => 'abgestaubt', 'reward' => '2,000 Sprite Dust'],
        ['code' => 'Perlimpinpin', 'reward' => '2,000 Sprite Dust'],
        ['code' => 'REACHYOURIMPOSSIBLE', 'reward' => 'Block Party Loading Screen'],
        ['code' => 'BeMoreAlien', 'reward' => 'Override Ready Loading Screen'],
        ['code' => 'LetsBlockAndRoll', 'reward' => 'Transforms you into a Tetris block temporarily'],
        ['code' => 'DontBlockMe', 'reward' => 'Transforms you into a Tetris block temporarily'],
        ['code' => 'NOPROLLAMA', 'reward' => '1x Llama Supply Drop', 'is_expired' => true],
    ];

    /**
     * Insert new codes and refresh existing ones without touching redemption status.
     *
     * Codes are inserted oldest first so newer codes receive higher IDs.
     */
    public function run(): void
    {
        $lobbyCodes = collect(self::LOBBY_CODES)
            ->reverse()
            ->map(fn (array $lobbyCode): array => [
                'code' => $lobbyCode['code'],
                'reward' => $lobbyCode['reward'],
                'requirement' => $lobbyCode['requirement'] ?? null,
                'is_expired' => $lobbyCode['is_expired'] ?? false,
            ])
            ->values()
            ->all();

        LobbyCode::upsert($lobbyCodes, uniqueBy: ['code'], update: ['reward', 'requirement', 'is_expired']);
    }
}
