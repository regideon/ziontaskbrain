<?php

namespace App\Ai\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class GetStatsTool implements Tool
{
    public function description(): Stringable|string
    {
        return 'Get live productivity stats: total, pending, completed, overdue, in-progress counts. ' .
               'Call this before generating daily summaries or productivity advice.';
    }


    public function schema(JsonSchema $schema): array
    {
        return [ 'user_id' => $schema->integer()->required() ];
    }


    public function handle(Request $request): Stringable|string
    {
        $base = Task::where('user_id', $request['user_id']);
        return json_encode([
            'total'       => (clone $base)->count(),
            'pending'     => (clone $base)->pending()->count(),
            'in_progress' => (clone $base)->where('status','in_progress')->count(),
            'completed'   => (clone $base)->completed()->count(),
            'overdue'     => (clone $base)->overdue()->count(),
            'ai_scored'   => (clone $base)->whereNotNull('ai_priority_score')->count(),
        ]);
    }

}
