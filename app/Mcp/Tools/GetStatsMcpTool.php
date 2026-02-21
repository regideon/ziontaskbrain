<?php

namespace App\Mcp\Tools;

use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get_stats')]
#[Description('Get productivity stats for the authenticated user: total, pending, in-progress, completed, overdue, and AI-scored counts.')]
#[IsReadOnly]
class GetStatsMcpTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $authUserId = $request->user()?->id;
        $userId = $validated['user_id'] ?? $authUserId;

        if (is_null($userId)) {
            return Response::error('Missing user_id and no authenticated user found.');
        }

        if (! is_null($authUserId) && $userId !== $authUserId) {
            return Response::error('You can only retrieve your own stats.');
        }

        $base = Task::query()->where('user_id', $userId);

        return Response::json([
            'success' => true,
            'total' => (clone $base)->count(),
            'pending' => (clone $base)->pending()->count(),
            'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
            'completed' => (clone $base)->completed()->count(),
            'overdue' => (clone $base)->overdue()->count(),
            'ai_scored' => (clone $base)->whereNotNull('ai_priority_score')->count(),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'user_id' => $schema->integer()->description('Optional. Defaults to authenticated user id.'),
        ];
    }
}
