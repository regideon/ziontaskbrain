<?php

namespace App\Ai\Tools;


use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;


class CreateTaskTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Create a new task from structured data. Call this after parsing natural language ' .
               'into a title, priority, and optional due date.';
    }


    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id'     => $schema->integer()->required(),
            'title'       => $schema->string()->required(),
            'description' => $schema->string(),
            'priority'    => $schema->string()->enum(['low','medium','high','urgent'])->default('medium'),
            'due_date'    => $schema->string(),  // YYYY-MM-DD or null
            'category_id' => $schema->integer(),
            'tags'        => $schema->array()->items($schema->string()),
        ];
    }


    public function handle(Request $request): Stringable|string
    {
        $rawCategoryId = $request['category_id'] ?? null;
        $categoryId = (is_numeric($rawCategoryId) && (int) $rawCategoryId > 0)
            ? (int) $rawCategoryId
            : null;

        $task = Task::create([
            'user_id'     => $request['user_id'],
            'title'       => $request['title'],
            'description' => $request['description'] ?? null,
            'priority'    => $request['priority'] ?? 'medium',
            'due_date'    => $request['due_date'] ?? null,
            'category_id' => $categoryId,
            'tags'        => $request['tags'] ?? [],
        ]);


        return json_encode(['success' => true, 'task_id' => $task->id, 'title' => $task->title]);
    }
}
