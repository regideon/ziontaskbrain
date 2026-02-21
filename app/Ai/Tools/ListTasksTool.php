<?php

namespace App\Ai\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;


class ListTasksTool implements Tool
{
    /**
     * Describe the tool's purpose so the AI knows when to call it.
     */
    public function description(): Stringable|string
    {
        return 'Retrieve tasks for a user. Filter by status (pending, in_progress, completed, cancelled), ' .
               'priority (low, medium, high, urgent), or only overdue tasks. Call this first ' .
               'before prioritizing or generating summaries.';
    }


    /**
     * Define the input schema using Laravel's fluent JsonSchema builder.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id'      => $schema->integer()->required(),
            'status'       => $schema->string()->enum(['pending','in_progress','completed','cancelled']),
            'priority'     => $schema->string()->enum(['low','medium','high','urgent']),
            'overdue_only' => $schema->boolean()->default(false),
            'limit'        => $schema->integer()->default(20),
        ];
    }


    /**
     * Execute the tool. The agent calls this automatically.
     * \$request acts like an array — access values with \$request['key'].
     */
    public function handle(Request $request): Stringable|string
    {
        $query = Task::where('user_id', $request['user_id'])->with('category');


        if (!empty($request['status']))
            $query->where('status', $request['status']);


        if (!empty($request['priority']))
            $query->where('priority', $request['priority']);


        if (!empty($request['overdue_only']))
            $query->overdue();


        $tasks = $query->latest()->limit($request['limit'] ?? 20)->get();


        // Return JSON string — the agent reads and reasons over this
        return json_encode([
            'total' => $tasks->count(),
            'tasks' => $tasks->map(fn($t) => [
                'id'                => $t->id,
                'title'             => $t->title,
                'description'       => $t->description,
                'status'            => $t->status,
                'priority'          => $t->priority,
                'due_date'          => $t->due_date?->toDateString(),
                'is_overdue'        => $t->isOverdue(),
                'ai_priority_score' => $t->ai_priority_score,
                'tags'              => $t->tags ?? [],
            ])->toArray(),
        ]);
    }

}
