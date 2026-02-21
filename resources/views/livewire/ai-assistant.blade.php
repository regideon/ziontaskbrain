<article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#1a2b2e]">
    <h2 class="mb-4 text-sm font-bold uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">AI Agent Console</h2>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-[#101f22]">
            <h3 class="mb-2 text-sm font-semibold text-emerald-700 dark:text-emerald-300">TaskCreatorAgent</h3>
            <div class="mb-3 rounded-lg border border-emerald-200 bg-emerald-50 p-3 text-xs dark:border-emerald-900 dark:bg-emerald-900/20">
                <div class="mb-2 flex items-center justify-between">
                    <p class="font-semibold text-emerald-700 dark:text-emerald-300">Example {{ $taskExampleIndex + 1 }} of 31</p>
                    <div class="flex gap-1">
                        <button type="button" wire:click="prevTaskExample" class="rounded-md border border-emerald-300 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 dark:border-emerald-800 dark:text-emerald-300">Previous</button>
                        <button type="button" wire:click="nextTaskExample" class="rounded-md border border-emerald-300 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 dark:border-emerald-800 dark:text-emerald-300">Next</button>
                    </div>
                </div>
                <p>{{ $this->currentTaskExample() }}</p>
            </div>
            <form wire:submit="createWithAgent" class="space-y-2">
                <textarea wire:model="taskPrompt" rows="3" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-[#0f1a1d]"></textarea>
                @error('taskPrompt')
                    <p class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                @enderror
                <button type="submit" wire:loading.attr="disabled" wire:target="createWithAgent" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60">
                    <span wire:loading.remove wire:target="createWithAgent">Create via Agent</span>
                    <span wire:loading wire:target="createWithAgent" class="inline-flex items-center gap-2">
                        <svg class="size-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </form>
            @if ($creatorOutput !== '')
                @php
                    $creatorJson = json_decode($creatorOutput, true);
                @endphp
                @if (is_array($creatorJson))
                    <pre class="mt-3 max-h-40 overflow-auto rounded-lg bg-white p-3 text-xs whitespace-pre-wrap dark:bg-[#0f1a1d]">{{ json_encode($creatorJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                @else
                    <div class="prose prose-sm mt-3 max-h-40 overflow-auto rounded-lg bg-white p-3 dark:prose-invert dark:bg-[#0f1a1d]">
                        {!! \Illuminate\Support\Str::markdown($creatorOutput) !!}
                    </div>
                @endif
            @endif
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-[#101f22]">
            <h3 class="mb-2 text-sm font-semibold text-amber-700 dark:text-amber-300">DailySummaryAgent</h3>
            <div class="mb-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs dark:border-amber-900 dark:bg-amber-900/20">
                <div class="mb-2 flex items-center justify-between">
                    <p class="font-semibold text-amber-700 dark:text-amber-300">Example {{ $summaryExampleIndex + 1 }} of 31</p>
                    <div class="flex gap-1">
                        <button type="button" wire:click="prevSummaryExample" class="rounded-md border border-amber-300 px-2 py-0.5 text-[11px] font-semibold text-amber-700 dark:border-amber-800 dark:text-amber-300">Previous</button>
                        <button type="button" wire:click="nextSummaryExample" class="rounded-md border border-amber-300 px-2 py-0.5 text-[11px] font-semibold text-amber-700 dark:border-amber-800 dark:text-amber-300">Next</button>
                    </div>
                </div>
                <p>{{ $this->currentSummaryExample() }}</p>
            </div>
            <form wire:submit="summarizeDay" class="space-y-2">
                <textarea wire:model="summaryPrompt" rows="3" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-[#0f1a1d]"></textarea>
                @error('summaryPrompt')
                    <p class="text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
                @enderror
                <button type="submit" wire:loading.attr="disabled" wire:target="summarizeDay" class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60">
                    <span wire:loading.remove wire:target="summarizeDay">Generate Summary</span>
                    <span wire:loading wire:target="summarizeDay" class="inline-flex items-center gap-2">
                        <svg class="size-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
            </form>
            @if ($summaryOutput !== '')
                <div class="prose prose-sm mt-3 max-h-48 overflow-auto rounded-lg bg-white p-3 dark:prose-invert dark:bg-[#0f1a1d]">
                    {!! \Illuminate\Support\Str::markdown($summaryOutput) !!}
                </div>
            @endif
        </div>
    </div>

    @if ($error !== '')
        <p class="mt-3 rounded-lg border border-rose-300 bg-rose-50 p-2 text-xs text-rose-700 dark:border-rose-900 dark:bg-rose-900/20 dark:text-rose-200">{{ $error }}</p>
    @endif
</article>
