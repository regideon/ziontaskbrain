<div class="min-h-screen bg-[#f6f7f8] text-slate-900 dark:bg-[#101c22] dark:text-slate-100">
    <div class="mx-auto flex min-h-screen w-full max-w-5xl flex-col pb-24">
        @include('livewire.partials.app-header', ['active' => 'tasks', 'subtitle' => 'Task Board'])

        {{-- <section class="p-4">
            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-800/40">
                <label for="active-user" class="mb-2 block text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active User</label>
                <select id="active-user" wire:model.live="userId" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @foreach ($this->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>
        </section> --}}

        <section class="px-4 py-2">
            <article class="rounded-xl border border-violet-200 bg-violet-50 p-4 dark:border-violet-900 dark:bg-violet-900/20">
                <div class="mb-3">
                    <h2 class="text-base font-bold text-violet-800 dark:text-violet-200">AI Helper</h2>
                </div>

                <form wire:submit="createViaAgentQuick" class="space-y-2">
                    <textarea wire:model="aiQuickPrompt" rows="2" placeholder="Add task naturally: Tomorrow 9am call investor, high priority, tag finance." class="w-full rounded-lg border border-violet-200 bg-white px-3 py-2 text-sm dark:border-violet-700 dark:bg-slate-900"></textarea>
                    @error('aiQuickPrompt')
                        <p class="text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                    <div class="space-y-2">
                        <button type="submit" wire:loading.attr="disabled" wire:target="createViaAgentQuick" class="w-full rounded-lg bg-violet-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60">Create Task</button>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" wire:click="generateBoardSummary" wire:loading.attr="disabled" wire:target="generateBoardSummary" class="w-full rounded-lg border border-violet-300 bg-white px-3 py-1.5 text-xs font-semibold text-violet-700 disabled:opacity-60 dark:border-violet-700 dark:bg-slate-900 dark:text-violet-300">Generate My Summary</button>
                            <button type="button" wire:click="runBoardPrioritizer" wire:loading.attr="disabled" wire:target="runBoardPrioritizer" class="w-full rounded-lg border border-violet-300 bg-white px-3 py-1.5 text-xs font-semibold text-violet-700 disabled:opacity-60 dark:border-violet-700 dark:bg-slate-900 dark:text-violet-300">Run Prioritizer</button>
                        </div>
                    </div>
                </form>

                @if ($aiActionError !== '')
                    <p class="mt-3 rounded-lg border border-rose-300 bg-rose-50 p-2 text-xs text-rose-700 dark:border-rose-900 dark:bg-rose-900/20 dark:text-rose-200">{{ $aiActionError }}</p>
                @endif

                @if ($aiCreatorOutput !== '')
                    <div class="mt-3 rounded-lg border border-violet-200 bg-white p-3 text-xs dark:border-violet-800 dark:bg-slate-900">
                        <p class="mb-1 font-semibold text-violet-700 dark:text-violet-300">TaskCreatorAgent output</p>
                        <pre class="whitespace-pre-wrap">{{ $aiCreatorOutput }}</pre>
                    </div>
                @endif

                @if ($aiSummaryOutput !== '')
                    <div class="prose prose-sm mt-3 max-w-none rounded-lg border border-violet-200 bg-white p-3 dark:prose-invert dark:border-violet-800 dark:bg-slate-900">
                        <p class="mb-1 font-semibold text-violet-700 dark:text-violet-300">DailySummaryAgent output</p>
                        {!! \Illuminate\Support\Str::markdown($aiSummaryOutput) !!}
                    </div>
                @endif

                @if ($aiPrioritizerOutput !== '')
                    <div class="prose prose-sm mt-3 max-w-none rounded-lg border border-violet-200 bg-white p-3 dark:prose-invert dark:border-violet-800 dark:bg-slate-900">
                        <p class="mb-1 font-semibold text-violet-700 dark:text-violet-300">TaskPriorizerAgent output</p>
                        {!! \Illuminate\Support\Str::markdown($aiPrioritizerOutput) !!}
                    </div>
                @endif
            </article>
        </section>

        @if ($showOnboarding)
            <section class="px-4 pb-2">
                <article class="rounded-xl border border-sky-200 bg-sky-50 p-4 dark:border-sky-900 dark:bg-sky-900/20">
                    <div class="mb-2 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-sky-800 dark:text-sky-200">Welcome to ZionTaskAI</h2>
                        <button wire:click="dismissOnboarding" class="rounded-md border border-sky-300 px-2 py-0.5 text-xs text-sky-700 dark:border-sky-700 dark:text-sky-300">Dismiss</button>
                    </div>
                    <ol class="list-decimal space-y-1 pl-5 text-sm text-sky-900 dark:text-sky-100">
                        <li>Create 2-3 tasks here in Task Board.</li>
                        <li>Open <a href="{{ route('agents-lab') }}" class="font-semibold underline">/agents</a> and try <code>Create via Agent</code>, <code>Generate Summary</code>, and <code>Run Prioritizer</code>.</li>
                        <li>Open <a href="{{ route('mcp-playground') }}" class="font-semibold underline">/mcp-playground</a> and run <code>initialize</code> then <code>list_tasks</code>.</li>
                    </ol>
                </article>
            </section>
        @endif

        <section class="px-4 pb-2">
            <div class="flex justify-end">
                <button
                    type="button"
                    wire:click="toggleMorningBriefing"
                    class="rounded-lg border border-indigo-300 bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700 dark:border-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-300"
                >
                    {{ $showMorningBriefing ? 'Hide Morning Briefing' : 'Show Morning Briefing' }}
                </button>
            </div>
        </section>

        @if ($showMorningBriefing && $morningBriefing !== '')
            <section class="px-4 pb-2">
                <article class="rounded-xl border border-indigo-200 bg-indigo-50 p-4 dark:border-indigo-900 dark:bg-indigo-900/20">
                    <div class="mb-2 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-indigo-800 dark:text-indigo-200">Morning Briefing</h2>
                        <button wire:click="dismissMorningBriefing" class="rounded-md border border-indigo-300 px-2 py-0.5 text-xs text-indigo-700 dark:border-indigo-700 dark:text-indigo-300">Dismiss</button>
                    </div>
                    <div class="prose prose-sm max-w-none text-indigo-900 dark:prose-invert dark:text-indigo-100">
                        {!! \Illuminate\Support\Str::markdown($morningBriefing) !!}
                    </div>
                </article>
            </section>
        @endif

        <section class="px-4 pb-2">
            @php
                $insights = $this->proactiveInsights;
                $leadInsight = $insights[0] ?? 'No insights yet. Add tasks to activate AI guidance.';
                $secondaryInsights = array_slice($insights, 1, 2);
                $stats = $this->stats;
                $toneClass = $stats['overdue'] > 0
                    ? 'bg-rose-500/20 text-rose-100 border border-rose-400/30'
                    : 'bg-emerald-500/20 text-emerald-100 border border-emerald-400/30';
            @endphp

            <div class="flex flex-col gap-4">
                <div class="relative overflow-hidden rounded-[1.5rem] bg-[#111418] p-5 text-white shadow-lg dark:bg-[#0d1419]">
                    <div class="absolute -right-10 -top-10 size-40 rounded-full bg-cyan-500/30 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 size-24 rounded-full bg-violet-500/20 blur-2xl"></div>

                    <div class="relative z-10 flex items-start gap-4">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/10 text-white backdrop-blur-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="m12 3 1.9 3.9L18 8.8l-3 2.9.7 4.1L12 14l-3.7 1.8.7-4.1-3-2.9 4.1-.9L12 3Z"/>
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-cyan-300/90">AI Insights</p>
                            <h4 class="mt-1 text-[15px] font-bold">Your Task Intelligence</h4>
                            <p class="mt-1 text-[13px] font-medium leading-relaxed text-slate-300">
                                {{ $leadInsight }}
                            </p>

                            @if (!empty($secondaryInsights))
                                <div class="mt-2 flex flex-wrap gap-2 text-[12px] font-semibold text-slate-300">
                                    @foreach ($secondaryInsights as $line)
                                        <span class="rounded-md bg-white/10 px-2 py-1">{{ $line }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-3 flex flex-wrap gap-3 text-[12px] font-semibold text-slate-400">
                                <span>Pending: <span class="text-white">{{ $stats['pending'] }}</span></span>
                                <span>Overdue: <span class="text-white">{{ $stats['overdue'] }}</span></span>
                                <span class="rounded-sm px-1 {{ $toneClass }}">AI Scored: <span class="text-white">{{ $stats['ai_scored'] }}</span></span>
                            </div>

                            <div class="mt-1 text-[11px] font-semibold text-slate-500">
                                {{ now()->format('F j, Y') }} • Live AI signals from your board
                            </div>
                        </div>
                    </div>

                    <a
                        href="{{ route('agents-lab') }}"
                        class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-xl bg-cyan-500/10 px-4 py-2 text-center text-sm font-bold text-cyan-300 transition-colors hover:bg-cyan-500/20"
                    >
                        Open Full Agent Console
                    </a>
                </div>
            </div>
        </section>

        <section class="px-4 pb-2">
            <div class="flex gap-3 overflow-x-auto pb-2">
                @foreach ($this->stats as $key => $value)
                    @php
                        $isOverdue = $key === 'overdue';
                        $isPrimary = in_array($key, ['total', 'pending', 'ai_scored'], true);
                    @endphp
                    <article class="@if($isOverdue) bg-orange-50 border-orange-200 dark:bg-orange-900/20 dark:border-orange-900/30 @elseif($isPrimary) bg-sky-50 border-sky-100 dark:bg-sky-500/10 dark:border-sky-500/20 @else bg-slate-50 border-slate-100 dark:bg-slate-800 dark:border-slate-700 @endif min-w-[120px] rounded-xl border p-4">
                        <p class="@if($isOverdue) text-orange-600 dark:text-orange-300 @elseif($isPrimary) text-sky-600 dark:text-sky-300 @else text-slate-500 dark:text-slate-400 @endif text-[10px] font-semibold uppercase tracking-wider">
                            {{ str_replace('_', ' ', $key) }}
                        </p>
                        <p class="mt-1 text-2xl font-bold">{{ $value }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section id="create-task" class="px-4 py-2">
            <article class="rounded-xl border border-slate-100 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                <div class="mb-3 flex items-center justify-between">
                    <h2 class="text-base font-bold">Create Task</h2>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="toggleCreateTaskForm" class="text-xs font-semibold text-sky-600 underline-offset-2 hover:underline dark:text-sky-300">
                            {{ $showCreateTaskForm ? 'Hide form' : 'Show form' }}
                        </button>
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400">Manual Tool UI</span>
                    </div>
                </div>

                @if ($showCreateTaskForm)
                    <form wire:submit="createTask" class="grid gap-2 sm:grid-cols-2">
                        <input wire:model="title" type="text" placeholder="Task title" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900 sm:col-span-2" />
                        <textarea wire:model="description" rows="3" placeholder="Description" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900 sm:col-span-2"></textarea>
                        <select wire:model="priority" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900">
                            <option value="low">low</option>
                            <option value="medium">medium</option>
                            <option value="high">high</option>
                            <option value="urgent">urgent</option>
                        </select>
                        <input wire:model="dueDate" type="date" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900" />
                        <div class="flex gap-2 sm:col-span-2">
                            <input wire:model="tagInput" wire:keydown.enter.prevent="addTag" type="text" placeholder="Add tag and press enter" class="flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900" />
                            <button type="button" wire:click="addTag" class="rounded-lg border border-sky-300 bg-sky-50 px-4 py-2 text-xs font-semibold text-sky-700 dark:border-sky-500/40 dark:bg-sky-500/20 dark:text-sky-200">Add</button>
                        </div>
                        @if (!empty($tags))
                            <div class="flex flex-wrap gap-2 sm:col-span-2">
                                @foreach ($tags as $tag)
                                    <button type="button" wire:click="removeTag('{{ $tag }}')" class="rounded-full border border-slate-200 bg-slate-100 px-3 py-1 text-xs dark:border-slate-600 dark:bg-slate-700">{{ $tag }} ×</button>
                                @endforeach
                            </div>
                        @endif
                        <button type="submit" class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Create Task</button>
                    </form>
                @else
                    <p class="text-sm text-slate-500 dark:text-slate-400">Create Task form is hidden.</p>
                @endif
            </article>
        </section>

        <section class="px-4 py-2 mt-5">
            @if ($editFeedbackMessage !== '')
                <div class="@if($editFeedbackType === 'success') border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-200 @else border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900 dark:bg-rose-900/20 dark:text-rose-200 @endif mb-3 flex items-center justify-between rounded-xl border px-4 py-3 text-sm">
                    <p>{{ $editFeedbackMessage }}</p>
                    <button wire:click="clearEditFeedback" class="rounded-md border border-current/30 px-2 py-0.5 text-xs">Dismiss</button>
                </div>
            @endif

            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-bold">Tasks</h2>
                <div class="flex gap-2">
                    <select wire:model.live="statusFilter" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-800">
                        <option value="all">all status</option>
                        <option value="pending">pending</option>
                        <option value="in_progress">in progress</option>
                        <option value="completed">completed</option>
                        <option value="cancelled">cancelled</option>
                    </select>
                    <select wire:model.live="priorityFilter" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs dark:border-slate-700 dark:bg-slate-800">
                        <option value="all">all priority</option>
                        <option value="low">low</option>
                        <option value="medium">medium</option>
                        <option value="high">high</option>
                        <option value="urgent">urgent</option>
                    </select>
                </div>
            </div>

            <main class="space-y-3 pb-8">
                @forelse ($this->tasks as $task)
                    @php
                        $priorityClass = match ($task->priority) {
                            'urgent', 'high' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                            'medium' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                            default => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
                        };
                        $statusClass = match ($task->status) {
                            'pending' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300',
                            'completed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'in_progress' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
                            'cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
                            default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                        };
                        $nudge = null;
                        if ($task->isOverdue()) {
                            $nudge = 'Overdue risk: do this today to prevent backlog growth.';
                        } elseif (($task->ai_priority_score ?? 0) >= 90) {
                            $nudge = 'AI says do this next.';
                        } elseif (in_array($task->priority, ['low', 'medium'], true) && ($task->ai_priority_score ?? 0) < 70) {
                            $nudge = 'Can be postponed after top-priority work.';
                        }
                    @endphp
                    <article wire:click="startEdit({{ $task->id }})" class="cursor-pointer rounded-xl border border-slate-100 bg-white p-4 shadow-sm transition-colors hover:border-sky-200 dark:border-slate-700 dark:bg-slate-800 dark:hover:border-sky-800">
                        <div class="mb-2 flex items-start justify-between gap-3">
                            <span class="{{ $priorityClass }} rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide">{{ $task->priority }} priority</span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500">
                                @if ($task->due_date) {{ $task->due_date->toDateString() }} @else no due date @endif
                            </span>
                        </div>
                        <h3 class="mb-1 truncate text-base font-bold" title="{{ $task->title }}">{{ $task->title }}</h3>
                        <p class="line-clamp-2 text-sm text-slate-500 dark:text-slate-400" title="{{ $task->description }}">{{ $task->description }}</p>
                        @if ($nudge)
                            <p class="mt-2 text-[11px] font-medium text-cyan-700 dark:text-cyan-300">{{ $nudge }}</p>
                        @endif

                        <div class="mt-3 flex items-center justify-between">
                            <div class="flex flex-wrap gap-1.5 text-[11px]">
                                <span class="{{ $statusClass }} rounded-full px-2 py-0.5 text-[10px] font-semibold">{{ str_replace('_', ' ', $task->status) }}</span>
                                <span class="rounded-full bg-sky-100 px-2 py-0.5 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">AI {{ $task->ai_priority_score ?? 'n/a' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($task->status !== 'completed')
                                    <button wire:click.stop="completeTask({{ $task->id }})" class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-[10px] font-medium text-emerald-700 hover:bg-emerald-100 dark:border-emerald-900 dark:bg-emerald-900/20 dark:text-emerald-300 dark:hover:bg-emerald-900/30">Complete</button>
                                @else
                                    <button wire:click.stop="reopenTask({{ $task->id }})" class="rounded-md border border-slate-300 bg-white px-2 py-1 text-[10px] font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">Reopen</button>
                                @endif
                                <button
                                    wire:click.stop="deleteTask({{ $task->id }})"
                                    onclick="if (!confirm('Delete this task permanently?')) { event.stopImmediatePropagation(); return false; }"
                                    class="rounded-md border border-rose-200 bg-rose-50 px-2 py-1 text-[10px] font-medium text-rose-700 hover:bg-rose-100 dark:border-rose-900 dark:bg-rose-900/20 dark:text-rose-300 dark:hover:bg-rose-900/30"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-400">
                        <p>No tasks yet for this user. Use a sample prompt to see AI power quickly:</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($this->sampleTaskPrompts() as $index => $prompt)
                                <button type="button" wire:click="useSamplePrompt({{ $index }})" class="rounded-full border border-slate-300 bg-slate-50 px-3 py-1 text-xs text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200">
                                    Try sample {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforelse

                @if ($editingTaskId)
                    <article id="task-edit-panel" class="rounded-xl border border-cyan-200 bg-cyan-50 p-4 shadow-sm dark:border-cyan-900 dark:bg-cyan-900/20">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-sm font-bold">Edit Task #{{ $editingTaskId }}</h3>
                            <button wire:click="cancelEdit" class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs dark:border-slate-600">Cancel</button>
                        </div>
                        <form wire:submit="saveEdit" class="grid gap-2 sm:grid-cols-2">
                            <input wire:model="editTitle" type="text" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900 sm:col-span-2" />
                            @error('editTitle')
                                <p class="text-xs text-rose-600 sm:col-span-2">{{ $message }}</p>
                            @enderror
                            <textarea wire:model="editDescription" rows="3" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900 sm:col-span-2"></textarea>
                            @error('editDescription')
                                <p class="text-xs text-rose-600 sm:col-span-2">{{ $message }}</p>
                            @enderror
                            <select wire:model="editPriority" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900">
                                <option value="low">low</option>
                                <option value="medium">medium</option>
                                <option value="high">high</option>
                                <option value="urgent">urgent</option>
                            </select>
                            @error('editPriority')
                                <p class="text-xs text-rose-600 sm:col-span-2">{{ $message }}</p>
                            @enderror
                            <input wire:model="editDueDate" type="date" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-slate-900" />
                            @error('editDueDate')
                                <p class="text-xs text-rose-600 sm:col-span-2">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="rounded-lg bg-cyan-600 px-4 py-2 text-sm font-semibold text-white sm:col-span-2">Save Task Changes</button>
                        </form>
                    </article>
                @endif

                @if ($this->tasks->count() < $this->totalFilteredTasks)
                    <button wire:click="loadMoreTasks" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        Display more tasks
                    </button>
                @endif
            </main>
        </section>
    </div>

    <script>
        (() => {
            const scrollToEdit = () => {
                const panel = document.getElementById('task-edit-panel');
                if (!panel) return;
                panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            };

            window.addEventListener('task-edit-opened', scrollToEdit);
        })();
    </script>

    <button type="button" onclick="document.getElementById('create-task')?.scrollIntoView({ behavior: 'smooth' })" class="fixed bottom-24 right-6 z-40 flex size-14 items-center justify-center rounded-full bg-sky-500 text-white shadow-lg shadow-sky-500/30">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M12 5v14m-7-7h14"/>
        </svg>
    </button>

</div>
