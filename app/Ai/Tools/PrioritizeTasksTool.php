<?php

namespace App\Ai\Tools;


use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;


class PrioritizeTasksTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Save AI-generated priority scores (1-100) for tasks. Call list_tasks first ' .
               'to get pending tasks, then call this with a score and reasoning for each one.';
    }


    public function schema(JsonSchema $schema): array
    {
        return [
            'priority_scores' => $schema->array()->items(
                $schema->object([
                    'task_id'   => $schema->integer()->required(),
                    'score'     => $schema->integer()->min(1)->max(100)->required(),
                    'reasoning' => $schema->string(),
                ])
            )->required(),
        ];
    }


    public function handle(Request $request): Stringable|string
    {
        $updated = 0;
        foreach ($request['priority_scores'] as $item) {
            Task::where('id', $item['task_id'])->update([
                'ai_priority_score' => $item['score'],
                'ai_reasoning'      => $item['reasoning'] ?? null,
            ]);
            $updated++;
        }
        return json_encode(['success' => true, 'tasks_updated' => $updated]);
    }
}
