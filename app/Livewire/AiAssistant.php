<?php

namespace App\Livewire;

use App\Ai\Agents\DailySummaryAgent;
use App\Ai\Agents\TaskCreatorAgent;
use App\Models\Task;
use Livewire\Attributes\On;
use Livewire\Component;

class AiAssistant extends Component
{
    public int $userId = 0;
    public string $taskPrompt = '';
    public string $summaryPrompt = 'Give me a tight daily summary and next best actions.';
    public string $creatorOutput = '';
    public string $summaryOutput = '';
    public string $error = '';
    public int $taskExampleIndex = 0;
    public int $summaryExampleIndex = 0;

    public function mount(): void
    {
        $startIndex = (now()->day - 1) % 31;
        $this->taskExampleIndex = $startIndex;
        $this->summaryExampleIndex = $startIndex;
    }

    public function nextTaskExample(): void
    {
        $this->taskExampleIndex = ($this->taskExampleIndex + 1) % count($this->taskExamples());
    }

    public function prevTaskExample(): void
    {
        $count = count($this->taskExamples());
        $this->taskExampleIndex = ($this->taskExampleIndex - 1 + $count) % $count;
    }

    public function nextSummaryExample(): void
    {
        $this->summaryExampleIndex = ($this->summaryExampleIndex + 1) % count($this->summaryExamples());
    }

    public function prevSummaryExample(): void
    {
        $count = count($this->summaryExamples());
        $this->summaryExampleIndex = ($this->summaryExampleIndex - 1 + $count) % $count;
    }

    public function currentTaskExample(): string
    {
        $examples = $this->taskExamples();

        return $examples[$this->taskExampleIndex] ?? $examples[0];
    }

    public function currentSummaryExample(): string
    {
        $examples = $this->summaryExamples();

        return $examples[$this->summaryExampleIndex] ?? $examples[0];
    }

    public function taskExamples(): array
    {
        return [
            'Tomorrow at 7:30am review sales pipeline, high priority, tags sales,planning.',
            'Today 4pm send partnership follow-up email, medium priority, tag bizdev.',
            'Next Monday 9am prepare sprint kickoff agenda, high priority, tags team,sprint.',
            'Friday 2pm finalize invoice batch for clients, urgent, tag finance.',
            'Tonight 8pm draft product changelog, medium priority, tags product,release.',
            'Tomorrow 10am call supplier about delays, high priority, tags ops,supply.',
            'Today 1pm polish onboarding checklist, medium priority, tags ux,customer.',
            'Saturday 9am backup database and verify restore, urgent, tags devops,security.',
            'Tomorrow 6pm plan content calendar for next week, medium priority, tags marketing.',
            'Today 11am fix payment webhook bug, urgent, tags backend,bugfix.',
            'Next Tuesday 3pm record feature demo video, high priority, tags demo,launch.',
            'Tomorrow 9am prepare interview questions for candidate, medium priority, tags hiring.',
            'Friday 5pm submit tax documents to accountant, high priority, tags finance,compliance.',
            'Today 2pm update KPI dashboard formulas, medium priority, tags analytics.',
            'Tomorrow 8am write investor status update, high priority, tags investor,report.',
            'Monday 10am clean up stale support tickets, medium priority, tags support.',
            'Today 7pm review architecture for MCP tools, high priority, tags mcp,architecture.',
            'Tomorrow noon create social launch posts, medium priority, tags social,launch.',
            'Thursday 4pm test password reset flow end-to-end, high priority, tags qa,auth.',
            'Today 9:30am prep board meeting deck, urgent, tags strategy,leadership.',
            'Tomorrow 9am call investor, high priority, tag finance.',
            'Friday 11am prepare sermon outline notes, medium priority, tags personal,writing.',
            'Today 3pm optimize slow task query, urgent, tags performance,database.',
            'Tomorrow 2pm review design handoff with frontend dev, high priority, tags ui,frontend.',
            'Saturday 10am declutter backlog and archive old tasks, low priority, tags cleanup.',
            'Today 5pm send weekly team wins recap, medium priority, tags team,communication.',
            'Tomorrow 1pm draft FAQ for new feature, medium priority, tags docs,product.',
            'Monday 8am prioritize roadmap candidates, high priority, tags roadmap,planning.',
            'Today 6pm prepare release rollback checklist, high priority, tags release,safety.',
            'Tomorrow 4pm validate analytics tracking events, medium priority, tags analytics,qa.',
            'Sunday 3pm plan next week top 3 outcomes, medium priority, tags planning,focus.',
        ];
    }

    public function summaryExamples(): array
    {
        return [
            'Give me a short daily summary, top 3 priorities, and one immediate action to start now.',
            'Summarize my productivity today and tell me what to finish before end of day.',
            'Use my current task stats and tell me the best order to work in this morning.',
            'What should I focus on first if I only have 90 minutes available today?',
            'Give me a coach-style summary with one warning risk and one quick win.',
            'Summarize pending vs overdue and tell me what should be done in the next 2 hours.',
            'Create a practical plan for today using my task stats, no fluff.',
            'What is my current progress and what should I complete before noon?',
            'Based on my stats, what is the single most important task right now?',
            'Give me a strict top 3 list for today and one fallback plan if I get blocked.',
            'Summarize my day and recommend one task to delegate if possible.',
            'Show my progress in one sentence, then 3 priority actions.',
            'Tell me where I am falling behind and what to do first.',
            'Generate an end-of-day summary and what I should prepare for tomorrow.',
            'How can I reduce overdue tasks fastest based on my current numbers?',
            'Give me a focused work plan with two 25-minute sprints.',
            'Summarize my tasks and suggest a realistic completion target for today.',
            'What should I do first, second, and third if I want momentum?',
            'Give me priority guidance for high-impact work before low-impact tasks.',
            'Use my stats and suggest the next best task after I finish one item.',
            'Create a daily game plan from my current pending and overdue tasks.',
            'Give me a no-nonsense summary and one sentence I can act on now.',
            'Summarize what is urgent today and what can safely wait.',
            'What is the fastest path to a productive day based on my current board?',
            'Show me top priorities and one habit to avoid procrastination today.',
            'Give me an executive-style summary with clear immediate actions.',
            'Summarize my workload and tell me what to postpone responsibly.',
            'What should I tackle first to avoid deadline stress this week?',
            'Generate a productivity summary for today plus one evening wrap-up task.',
            'Use my stats to suggest the next 3 steps for maximum progress.',
            'Give me a concise daily summary with top priorities and tactical suggestion.',
        ];
    }

    public function createWithAgent(): void
    {
        $this->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'taskPrompt' => ['required', 'string', 'min:8'],
        ]);

        $this->error = '';

        try {
            $response = (new TaskCreatorAgent($this->userId))->prompt($this->taskPrompt);

            if (is_array($response)) {
                $this->creatorOutput = json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) ?: '';
            } else {
                $this->creatorOutput = (string) $response;
            }

            $this->taskPrompt = '';
            $this->dispatch('board-updated');
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    public function summarizeDay(): void
    {
        $this->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'summaryPrompt' => ['required', 'string', 'min:8'],
        ]);

        $this->error = '';
        $this->summaryOutput = '';

        try {
            $response = (new DailySummaryAgent($this->userId))->prompt($this->summaryPrompt);
            $text = trim((string) $response);

            if ($text === '') {
                $fallback = $this->tryBuildFallbackSummary();
                $this->summaryOutput = $fallback['summary'];
                $this->error = 'AI returned an empty response. '.$fallback['message'];

                return;
            }

            $this->summaryOutput = $text;
        } catch (\Throwable $e) {
            $fallback = $this->tryBuildFallbackSummary();
            $this->summaryOutput = $fallback['summary'];
            $this->error = 'AI service error: '.$e->getMessage().' '.$fallback['message'];
        }
    }

    private function tryBuildFallbackSummary(): array
    {
        try {
            return [
                'summary' => $this->buildFallbackSummary(),
                'message' => 'Showing fallback summary from your current tasks.',
            ];
        } catch (\Throwable $fallbackError) {
            return [
                'summary' => '',
                'message' => 'Fallback summary failed: '.$fallbackError->getMessage(),
            ];
        }
    }

    private function buildFallbackSummary(): string
    {
        $base = Task::query()->where('user_id', $this->userId);

        $total = (clone $base)->count();
        $pending = (clone $base)->where('status', 'pending')->count();
        $inProgress = (clone $base)->where('status', 'in_progress')->count();
        $completed = (clone $base)->where('status', 'completed')->count();
        $overdue = (clone $base)->overdue()->count();

        $topTasks = Task::query()
            ->where('user_id', $this->userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderByRaw('CASE WHEN due_date < CURDATE() THEN 0 ELSE 1 END')
            ->orderBy('due_date')
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'medium', 'low')")
            ->limit(3)
            ->get(['title', 'priority', 'due_date']);

        $topLines = $topTasks->map(function (Task $task, int $index): string {
            $due = $task->due_date ? $task->due_date->toDateString() : 'no due date';
            return ($index + 1).") **{$task->title}** ({$task->priority}, due {$due})";
        })->all();

        if (count($topLines) === 0) {
            $topLines = [
                '1) **No pending tasks found** (you can create one in Task Board).',
            ];
        }

        return "**Progress:** {$completed}/{$total} completed; {$pending} pending, {$inProgress} in progress, {$overdue} overdue.\n\n".
            "**Top priorities:**\n".implode("\n", $topLines)."\n\n".
            '**Tactical suggestion:** Start with the first task above for 25 minutes, then re-check priorities.';
    }

    #[On('board-updated')]
    public function noop(): void
    {
        // Forces dependent sections to refresh when parent triggers board updates.
    }

    public function render()
    {
        return view('livewire.ai-assistant');
    }
}
