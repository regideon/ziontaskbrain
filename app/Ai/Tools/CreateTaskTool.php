<?php

namespace App\Ai\Tools;


use App\Models\Task;
use DateTimeImmutable;
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
        $dueDate = $this->normalizeDueDate($request['due_date'] ?? null);
        $tags = $this->normalizeTags($request['tags'] ?? []);

        $task = Task::create([
            'user_id'     => $request['user_id'],
            'title'       => $request['title'],
            'description' => $request['description'] ?? null,
            'priority'    => $request['priority'] ?? 'medium',
            'due_date'    => $dueDate,
            'category_id' => $categoryId,
            'tags'        => $tags,
        ]);


        return json_encode(['success' => true, 'task_id' => $task->id, 'title' => $task->title]);
    }

    private function normalizeDueDate(mixed $rawDueDate): ?string
    {
        if (! is_string($rawDueDate)) {
            return null;
        }

        $value = trim($rawDueDate);

        if ($value === '' || in_array(strtolower($value), ['null', 'none', 'no due date'], true)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        $errors = DateTimeImmutable::getLastErrors();
        $hasErrors = is_array($errors) && (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0);

        if ($date === false || $hasErrors || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $value;
    }

    private function normalizeTags(mixed $rawTags): array
    {
        if (is_string($rawTags)) {
            $rawTags = explode(',', $rawTags);
        }

        if (! is_array($rawTags)) {
            return [];
        }

        $tags = [];

        foreach ($rawTags as $tag) {
            if (! is_string($tag)) {
                continue;
            }

            $cleanTag = trim($tag);

            if ($cleanTag === '' || in_array($cleanTag, $tags, true)) {
                continue;
            }

            $tags[] = $cleanTag;
        }

        return $tags;
    }
}
