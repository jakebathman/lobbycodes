<?php

namespace App\Models;

use Database\Factories\LobbyCodeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $reward
 * @property string|null $requirement
 * @property bool $is_expired
 * @property Carbon|null $redeemed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['code', 'reward', 'requirement', 'is_expired', 'redeemed_at'])]
class LobbyCode extends Model
{
    /** @use HasFactory<LobbyCodeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_expired' => 'boolean',
            'redeemed_at' => 'datetime',
        ];
    }

    /**
     * Scope the query to codes that can still be redeemed in game.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('is_expired', false);
    }

    /**
     * Scope the query to codes that have been redeemed.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function redeemed(Builder $query): void
    {
        $query->whereNotNull('redeemed_at');
    }

    /**
     * Scope the query to codes that have not been redeemed yet.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function unredeemed(Builder $query): void
    {
        $query->whereNull('redeemed_at');
    }
}
