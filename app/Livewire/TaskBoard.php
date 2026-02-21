<?php

namespace App\Livewire;

use App\Ai\Agents\DailySummaryAgent;
use App\Ai\Agents\TaskCreatorAgent;
use App\Ai\Agents\TaskPriorizerAgent;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TaskBoard extends Component
{
    public int $userId = 0;

    public string $title = '';
    public string $description = '';
    public string $priority = 'medium';
    public string $dueDate = '';
    public array $tags = [];
    public string $tagInput = '';

    public string $statusFilter = 'all';
    public string $priorityFilter = 'all';
    public int $tasksLimit = 5;

    public ?int $editingTaskId = null;
    public string $editTitle = '';
    public string $editDescription = '';
    public string $editPriority = 'medium';
    public string $editDueDate = '';
    public string $editFeedbackType = '';
    public string $editFeedbackMessage = '';
    public bool $showOnboarding = false;
    public bool $showCreateTaskForm = true;
    public string $aiQuickPrompt = '';
    public string $aiCreatorOutput = '';
    public string $aiSummaryOutput = '';
    public string $aiPrioritizerOutput = '';
    public string $aiActionError = '';
    public string $morningBriefing = '';
    public bool $showMorningBriefing = false;

    public function mount(): void
    {
        $userId = auth()->id();
        $this->userId = $userId ? (int) $userId : 0;
        $this->showOnboarding = (bool) session()->pull('show_onboarding', false);
        $this->loadMorningBriefing();
    }

    public function updatedUserId(): void
    {
        $this->tasksLimit = 5;
        $this->cancelEdit();
        $this->showMorningBriefing = false;
        $this->morningBriefing = '';
        $this->aiCreatorOutput = '';
        $this->aiSummaryOutput = '';
        $this->aiPrioritizerOutput = '';
        $this->aiActionError = '';
        $this->loadMorningBriefing();
    }

    public function createTask(): void
    {
        $this->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'dueDate' => ['nullable', 'date_format:Y-m-d'],
        ]);

        Task::query()->create([
            'user_id' => $this->userId,
            'title' => $this->title,
            'description' => $this->description ?: null,
            'priority' => $this->priority,
            'due_date' => $this->dueDate ?: null,
            'tags' => $this->tags,
        ]);

        $this->reset(['title', 'description', 'priority', 'dueDate', 'tags', 'tagInput']);
        $this->priority = 'medium';
        $this->dispatch('board-updated');
    }

    public function addTag(): void
    {
        $tag = trim($this->tagInput);

        if ($tag === '' || in_array($tag, $this->tags, true)) {
            $this->tagInput = '';

            return;
        }

        $this->tags[] = $tag;
        $this->tagInput = '';
    }

    public function removeTag(string $tag): void
    {
        $this->tags = array_values(array_filter(
            $this->tags,
            fn (string $item): bool => $item !== $tag
        ));
    }

    public function completeTask(int $taskId): void
    {
        Task::query()
            ->where('id', $taskId)
            ->where('user_id', $this->userId)
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

        $this->dispatch('board-updated');
    }

    public function reopenTask(int $taskId): void
    {
        Task::query()
            ->where('id', $taskId)
            ->where('user_id', $this->userId)
            ->update([
                'status' => 'pending',
                'completed_at' => null,
            ]);

        $this->dispatch('board-updated');
    }

    public function deleteTask(int $taskId): void
    {
        $task = Task::query()
            ->where('id', $taskId)
            ->where('user_id', $this->userId)
            ->first();

        if (! $task) {
            $this->editFeedbackType = 'error';
            $this->editFeedbackMessage = 'Task delete failed. Task was not found for this user.';

            return;
        }

        $task->delete();

        if ($this->editingTaskId === $taskId) {
            $this->cancelEdit();
        }

        $this->editFeedbackType = 'success';
        $this->editFeedbackMessage = 'Task deleted successfully.';
        $this->dispatch('board-updated');
    }

    public function updatedStatusFilter(): void
    {
        $this->tasksLimit = 5;
        $this->cancelEdit();
    }

    public function updatedPriorityFilter(): void
    {
        $this->tasksLimit = 5;
        $this->cancelEdit();
    }

    public function loadMoreTasks(): void
    {
        $this->tasksLimit += 5;
    }

    public function startEdit(int $taskId): void
    {
        $task = Task::query()
            ->where('id', $taskId)
            ->where('user_id', $this->userId)
            ->first();

        if (! $task) {
            return;
        }

        $this->editingTaskId = $task->id;
        $this->editTitle = $task->title;
        $this->editDescription = $task->description ?? '';
        $this->editPriority = $task->priority;
        $this->editDueDate = $task->due_date?->toDateString() ?? '';

        $this->dispatch('task-edit-opened');
    }

    public function cancelEdit(): void
    {
        $this->editingTaskId = null;
        $this->editTitle = '';
        $this->editDescription = '';
        $this->editPriority = 'medium';
        $this->editDueDate = '';
    }

    public function clearEditFeedback(): void
    {
        $this->editFeedbackType = '';
        $this->editFeedbackMessage = '';
    }

    public function dismissOnboarding(): void
    {
        $this->showOnboarding = false;
    }

    public function toggleCreateTaskForm(): void
    {
        $this->showCreateTaskForm = ! $this->showCreateTaskForm;
    }

    public function createViaAgentQuick(): void
    {
        $this->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'aiQuickPrompt' => ['required', 'string', 'min:8'],
        ]);

        $this->aiActionError = '';
        $this->aiCreatorOutput = '';

        try {
            $response = (new TaskCreatorAgent($this->userId))->prompt($this->aiQuickPrompt);
            $this->aiCreatorOutput = is_array($response)
                ? (json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '')
                : (string) $response;

            $this->aiQuickPrompt = '';
            $this->dispatch('board-updated');
        } catch (\Throwable $e) {
            $this->aiActionError = 'TaskCreatorAgent failed: '.$e->getMessage();
        }
    }

    public function generateBoardSummary(): void
    {
        $this->aiActionError = '';
        $this->aiSummaryOutput = '';

        try {
            $response = (new DailySummaryAgent($this->userId))
                ->prompt('Give me a concise daily summary with top 3 task names and one next action.');

            $summary = trim((string) $response);
            $this->aiSummaryOutput = $summary !== '' ? $summary : $this->buildFallbackSummary();
        } catch (\Throwable $e) {
            $this->aiSummaryOutput = $this->buildFallbackSummary();
            $this->aiActionError = 'DailySummaryAgent failed: '.$e->getMessage();
        }
    }

    public function runBoardPrioritizer(): void
    {
        $this->aiActionError = '';
        $this->aiPrioritizerOutput = '';

        try {
            $response = (new TaskPriorizerAgent($this->userId))
                ->prompt('Prioritize my pending tasks now and explain your ranking.');

            $this->aiPrioritizerOutput = (string) $response;
            $this->dispatch('board-updated');
        } catch (\Throwable $e) {
            $this->aiActionError = 'TaskPriorizerAgent failed: '.$e->getMessage();
        }
    }

    public function dismissMorningBriefing(): void
    {
        $this->showMorningBriefing = false;
    }

    public function useSamplePrompt(int $index): void
    {
        $prompts = $this->sampleTaskPrompts();
        $this->aiQuickPrompt = $prompts[$index] ?? '';
        $this->showCreateTaskForm = true;
    }

    public function sampleTaskPrompts(): array
    {
        return [
            'Tomorrow at 9am call investor, high priority, tag finance.',
            'Today 3pm fix login bug, urgent, tag engineering.',
            'Friday 11am write release notes, medium priority, tag product.',
        ];
    }

    private function loadMorningBriefing(): void
    {
        if ($this->userId <= 0) {
            return;
        }

        $cacheKey = 'morning-briefing:'.$this->userId.':'.now()->toDateString();
        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            $this->morningBriefing = $cached;
            $this->showMorningBriefing = true;

            return;
        }

        try {
            $briefing = (string) (new DailySummaryAgent($this->userId))
                ->prompt('Give me a short morning briefing with top 3 task names and first task to start now.');

            if (trim($briefing) === '') {
                $briefing = $this->buildFallbackSummary();
            }
        } catch (\Throwable) {
            $briefing = $this->buildFallbackSummary();
        }

        if (trim($briefing) !== '') {
            Cache::put($cacheKey, $briefing, now()->endOfDay());
            $this->morningBriefing = $briefing;
            $this->showMorningBriefing = true;
        }
    }

    private function buildFallbackSummary(): string
    {
        $base = Task::query()->where('user_id', $this->userId);
        $total = (clone $base)->count();
        $pending = (clone $base)->where('status', 'pending')->count();
        $completed = (clone $base)->where('status', 'completed')->count();
        $overdue = (clone $base)->overdue()->count();

        $topTasks = Task::query()
            ->where('user_id', $this->userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw('CASE WHEN ai_priority_score IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('ai_priority_score')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->limit(3)
            ->get(['title', 'priority', 'due_date']);

        $topLines = $topTasks->map(function (Task $task, int $index): string {
            $due = $task->due_date ? $task->due_date->toDateString() : 'no due date';

            return ($index + 1).") **{$task->title}** ({$task->priority}, due {$due})";
        })->all();

        if ($topLines === []) {
            $topLines = ['1) **No pending tasks yet**. Create one task to start momentum.'];
        }

        return "**Progress:** {$completed}/{$total} completed; {$pending} pending; {$overdue} overdue.\n\n".
            "**Top priorities:**\n".implode("\n", $topLines)."\n\n".
            '**Tactical suggestion:** Start with priority #1 for 25 focused minutes.';
    }

    public function saveEdit(): void
    {
        if ($this->editingTaskId === null) {
            return;
        }

        try {
            $validated = $this->validate([
                'editTitle' => ['required', 'string', 'max:255'],
                'editDescription' => ['nullable', 'string'],
                'editPriority' => ['required', 'in:low,medium,high,urgent'],
                'editDueDate' => ['nullable', 'date_format:Y-m-d'],
            ]);

            $updated = Task::query()
                ->where('id', $this->editingTaskId)
                ->where('user_id', $this->userId)
                ->update([
                    'title' => $validated['editTitle'],
                    'description' => $validated['editDescription'] ?: null,
                    'priority' => $validated['editPriority'],
                    'due_date' => $validated['editDueDate'] ?: null,
                ]);

            if ($updated === 0) {
                $this->editFeedbackType = 'error';
                $this->editFeedbackMessage = 'Task update failed. Task was not found for this user.';

                return;
            }

            $this->editFeedbackType = 'success';
            $this->editFeedbackMessage = 'Task updated successfully.';
            $this->cancelEdit();
            $this->dispatch('board-updated');
        } catch (\Throwable) {
            $this->editFeedbackType = 'error';
            $this->editFeedbackMessage = 'Task update failed. Please check the input and try again.';
        }
    }

    #[Computed]
    public function users(): Collection
    {
        if ($this->userId > 0) {
            return User::query()->whereKey($this->userId)->get(['id', 'name', 'email']);
        }

        return User::query()->whereRaw('1 = 0')->get(['id', 'name', 'email']);
    }

    #[Computed]
    public function tasks(): Collection
    {
        $query = Task::query()
            ->where('user_id', $this->userId);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }

        return $query
            ->orderByRaw('CASE WHEN ai_priority_score IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('ai_priority_score')
            ->orderBy('due_date')
            ->latest()
            ->limit($this->tasksLimit)
            ->get();
    }

    #[Computed]
    public function totalFilteredTasks(): int
    {
        $query = Task::query()->where('user_id', $this->userId);

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->priorityFilter !== 'all') {
            $query->where('priority', $this->priorityFilter);
        }

        return $query->count();
    }

    #[Computed]
    public function stats(): array
    {
        $base = Task::query()->where('user_id', $this->userId);

        return [
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->where('status', 'pending')->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'overdue' => (clone $base)->overdue()->count(),
            'ai_scored' => (clone $base)->whereNotNull('ai_priority_score')->count(),
        ];
    }

    #[Computed]
    public function proactiveInsights(): array
    {
        $stats = $this->stats();
        $insights = [];

        if ($stats['overdue'] > 0) {
            $insights[] = "You have {$stats['overdue']} overdue task(s). Clear one overdue item first.";
        }

        $nextTask = Task::query()
            ->where('user_id', $this->userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw('CASE WHEN ai_priority_score IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('ai_priority_score')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->orderBy('due_date')
            ->first();

        if ($nextTask) {
            $insights[] = 'Start with: "'.$nextTask->title.'" ('.
                $nextTask->priority.', due '.($nextTask->due_date?->toDateString() ?? 'no due date').').';
        }

        $quickWin = Task::query()
            ->where('user_id', $this->userId)
            ->where('status', 'pending')
            ->whereIn('priority', ['low', 'medium'])
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->first();

        if ($quickWin) {
            $insights[] = 'Quick win: "'.$quickWin->title.'" (target: 25-30 minutes).';
        }

        if ($insights === []) {
            $insights[] = 'No active tasks yet. Create one task and run AI actions to see insights.';
        }

        return $insights;
    }

    public function render()
    {
        return view('livewire.task-board')
            ->layout('components.layouts.livewire-main', [
                'title' => 'ZionTaskAI - Task Board',
            ]);
    }
}
