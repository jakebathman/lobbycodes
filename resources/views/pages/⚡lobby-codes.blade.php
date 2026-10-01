<?php

use App\Models\LobbyCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Title('Fortnite Lobby Codes')] class extends Component
{
    /**
     * Which codes to list: "all", "unused", or "used".
     */
    #[Url(except: 'all')]
    public string $filter = 'all';

    /**
     * Get the codes for the current filter, newest first with expired codes last.
     *
     * @return Collection<int, LobbyCode>
     */
    #[Computed]
    public function lobbyCodes(): Collection
    {
        return LobbyCode::query()
            ->when($this->filter === 'unused', fn (Builder $query) => $query->active()->unredeemed())
            ->when($this->filter === 'used', fn (Builder $query) => $query->redeemed())
            ->orderBy('is_expired')
            ->orderByDesc('id')
            ->get();
    }

    #[Computed]
    public function activeCodeCount(): int
    {
        return LobbyCode::active()->count();
    }

    #[Computed]
    public function redeemedActiveCodeCount(): int
    {
        return LobbyCode::active()->redeemed()->count();
    }

    public function setRedeemed(int $lobbyCodeId, bool $isRedeemed): void
    {
        $lobbyCode = LobbyCode::findOrFail($lobbyCodeId);

        $lobbyCode->update([
            'redeemed_at' => $isRedeemed ? ($lobbyCode->redeemed_at ?? now()) : null,
        ]);
    }
};
?>

<main class="mx-auto flex max-w-2xl flex-col gap-6 px-4 py-8 sm:py-12">
    <header class="flex flex-col gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-violet-600 dark:text-violet-400">Fortnite Admin Panel</p>
            <h1 class="text-3xl font-semibold tracking-tight">Lobby Codes</h1>
            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">
                Open the <span class="font-medium text-zinc-800 dark:text-zinc-200">.../admin panel</span> box in the top-right of the lobby, enter a code, and hit Submit.
            </p>
        </div>

        <div class="flex flex-col gap-2">
            <div class="flex items-baseline justify-between text-sm">
                <span class="font-medium">{{ $this->redeemedActiveCodeCount }} of {{ $this->activeCodeCount }} redeemed</span>
                @if ($this->activeCodeCount > 0 && $this->redeemedActiveCodeCount === $this->activeCodeCount)
                    <span class="text-emerald-600 dark:text-emerald-400">All done!</span>
                @endif
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800">
                <div
                    class="h-full rounded-full bg-violet-600 transition-all dark:bg-violet-500"
                    style="width: {{ $this->activeCodeCount > 0 ? round($this->redeemedActiveCodeCount / $this->activeCodeCount * 100) : 0 }}%"
                ></div>
            </div>
        </div>

        <nav class="flex gap-1 rounded-lg bg-zinc-200 p-1 text-sm dark:bg-zinc-800">
            @foreach (['all' => 'All', 'unused' => 'To redeem', 'used' => 'Redeemed'] as $filterValue => $filterLabel)
                <button
                    type="button"
                    wire:key="filter-{{ $filterValue }}"
                    wire:click="$set('filter', '{{ $filterValue }}')"
                    @class([
                        'flex-1 cursor-pointer rounded-md px-3 py-1.5 font-medium transition',
                        'bg-white shadow-sm dark:bg-zinc-950' => $filter === $filterValue,
                        'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100' => $filter !== $filterValue,
                    ])
                >
                    {{ $filterLabel }}
                </button>
            @endforeach
        </nav>
    </header>

    @if ($this->lobbyCodes->isEmpty())
        <p class="rounded-xl bg-white px-4 py-10 text-center text-sm text-zinc-500 shadow-sm ring-1 ring-zinc-200 dark:bg-zinc-900 dark:ring-zinc-800">
            {{ $filter === 'unused' ? 'Nothing left to redeem. Nice.' : 'No codes here yet.' }}
        </p>
    @else
        <ul class="divide-y divide-zinc-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-zinc-200 dark:divide-zinc-800 dark:bg-zinc-900 dark:ring-zinc-800">
            @foreach ($this->lobbyCodes as $lobbyCode)
                <li
                    wire:key="lobby-code-{{ $lobbyCode->id }}"
                    @class([
                        'group flex items-center gap-3 px-4 py-3 transition has-checked:bg-zinc-50 dark:has-checked:bg-zinc-900/40',
                        'opacity-60' => $lobbyCode->is_expired,
                    ])
                >
                    <label class="flex min-w-0 flex-1 cursor-pointer items-start gap-3">
                        <input
                            type="checkbox"
                            class="mt-0.5 size-5 shrink-0 cursor-pointer accent-violet-600"
                            aria-label="Mark {{ $lobbyCode->code }} as redeemed"
                            @checked($lobbyCode->redeemed_at)
                            wire:change="setRedeemed({{ $lobbyCode->id }}, $event.target.checked)"
                        >
                        <span class="flex min-w-0 flex-col gap-0.5">
                            <span class="flex flex-wrap items-center gap-2">
                                <span class="break-all font-mono font-semibold group-has-checked:text-zinc-400 group-has-checked:line-through dark:group-has-checked:text-zinc-500">{{ $lobbyCode->code }}</span>
                                @if ($lobbyCode->is_expired)
                                    <span class="rounded bg-zinc-200 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Expired</span>
                                @endif
                            </span>
                            <span class="text-sm text-zinc-600 group-has-checked:text-zinc-400 dark:text-zinc-400 dark:group-has-checked:text-zinc-500">{{ $lobbyCode->reward }}</span>
                            @if ($lobbyCode->requirement)
                                <span class="text-xs text-amber-700 dark:text-amber-400">Requires: {{ $lobbyCode->requirement }}</span>
                            @endif
                        </span>
                    </label>

                    <button
                        type="button"
                        x-data="{ copied: false }"
                        x-on:click="navigator.clipboard.writeText(@js($lobbyCode->code)); copied = true; setTimeout(() => copied = false, 1500)"
                        class="shrink-0 cursor-pointer rounded-md px-2.5 py-1 text-xs font-medium text-zinc-600 ring-1 ring-zinc-300 transition hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:ring-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-white"
                    >
                        <span x-text="copied ? 'Copied' : 'Copy'">Copy</span>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif

    <footer class="flex flex-col gap-1 text-xs text-zinc-500">
        <p>Redeeming a Sprite you already own gives 10,000 Sprite Dust instead.</p>
        <p>
            Codes from
            <a href="https://www.ign.com/wikis/fortnite/All_Admin_Panel_Lobby_Hack_Codes_For_Free_Rewards" target="_blank" rel="noopener" class="underline underline-offset-2 hover:text-zinc-700 dark:hover:text-zinc-300">IGN</a>.
        </p>
    </footer>
</main>
