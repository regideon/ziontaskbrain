<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#101f22] dark:text-slate-100">
    @include('livewire.partials.app-header', ['active' => 'agents', 'subtitle' => 'Agents Module'])

    <div class="mx-auto w-full max-w-6xl px-4 py-6">
        <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#1a2b2e]">
            <label for="user" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Active User</label>
            <select id="user" wire:model.live="userId" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-[#101f22]">
                @foreach ($this->users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1fr_1fr]">
            <livewire:ai-assistant :user-id="$userId" :key="'assistant-lab-'.$userId" />

            <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#1a2b2e]">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">TaskPriorizerAgent</h2>
                    <button wire:click="prioritizeWithAgent" wire:loading.attr="disabled" wire:target="prioritizeWithAgent" class="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60">
                        <span wire:loading.remove wire:target="prioritizeWithAgent">Run Prioritizer</span>
                        <span wire:loading wire:target="prioritizeWithAgent" class="inline-flex items-center gap-2">
                            <svg class="size-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>

                @if ($prioritizerSummary !== '')
                    <div class="prose prose-sm max-h-96 overflow-auto rounded-lg bg-slate-100 p-3 dark:prose-invert dark:bg-[#101f22]">
                        {!! \Illuminate\Support\Str::markdown($prioritizerSummary) !!}
                    </div>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">Click "Run Prioritizer" to let the agent score pending tasks.</p>
                @endif

                @if ($error !== '')
                    <p class="mt-3 rounded-lg border border-rose-300 bg-rose-50 p-2 text-xs text-rose-700 dark:border-rose-900 dark:bg-rose-900/20 dark:text-rose-200">{{ $error }}</p>
                @endif
            </article>
        </div>
    </div>
</div>
