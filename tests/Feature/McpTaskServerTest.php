<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class McpTaskServerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calls_each_task_tool_via_mcp_route(): void
    {
        $this->withoutMiddleware();

        $user = User::factory()->create();

        $initialize = $this->postJson('/mcp/tasks', [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [
                'protocolVersion' => '2025-06-18',
                'capabilities' => [],
                'clientInfo' => [
                    'name' => 'phpunit',
                    'version' => '1.0',
                ],
            ],
        ]);

        $initialize->assertOk()
            ->assertJsonPath('jsonrpc', '2.0')
            ->assertJsonPath('id', 1);

        $sessionId = $initialize->headers->get('MCP-Session-Id');
        $this->assertNotEmpty($sessionId);

        $createResult = $this->callTool(2, $sessionId, 'create_task', [
            'user_id' => $user->id,
            'title' => 'Write MCP test',
            'description' => 'Verify MCP tool integration',
            'priority' => 'high',
            'tags' => ['testing', 'mcp'],
        ]);

        $this->assertTrue($createResult['success']);
        $taskId = $createResult['task_id'];

        $listResult = $this->callTool(3, $sessionId, 'list_tasks', [
            'user_id' => $user->id,
        ]);

        $this->assertTrue($listResult['success']);
        $this->assertSame(1, $listResult['total']);
        $this->assertSame($taskId, $listResult['tasks'][0]['id']);

        $prioritizeResult = $this->callTool(4, $sessionId, 'prioritize_tasks', [
            'user_id' => $user->id,
            'priority_scores' => [
                [
                    'task_id' => $taskId,
                    'score' => 90,
                    'reasoning' => 'High-impact and urgent.',
                ],
            ],
        ]);

        $this->assertTrue($prioritizeResult['success']);
        $this->assertSame(1, $prioritizeResult['tasks_updated']);

        $completeResult = $this->callTool(5, $sessionId, 'complete_task', [
            'user_id' => $user->id,
            'task_id' => $taskId,
        ]);

        $this->assertTrue($completeResult['success']);
        $this->assertSame('completed', $completeResult['status']);

        $statsResult = $this->callTool(6, $sessionId, 'get_stats', [
            'user_id' => $user->id,
        ]);

        $this->assertTrue($statsResult['success']);
        $this->assertSame(1, $statsResult['total']);
        $this->assertSame(1, $statsResult['completed']);
        $this->assertSame(1, $statsResult['ai_scored']);
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    protected function callTool(int $id, string $sessionId, string $tool, array $arguments): array
    {
        $response = $this
            ->withHeader('MCP-Session-Id', $sessionId)
            ->postJson('/mcp/tasks', [
                'jsonrpc' => '2.0',
                'id' => $id,
                'method' => 'tools/call',
                'params' => [
                    'name' => $tool,
                    'arguments' => $arguments,
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('jsonrpc', '2.0')
            ->assertJsonPath('id', $id)
            ->assertJsonMissingPath('error');

        $payload = $response->json('result.content.0.text');
        $this->assertIsString($payload);

        $decoded = json_decode($payload, true);
        $this->assertIsArray($decoded, "Invalid JSON payload from [{$tool}]");

        return $decoded;
    }
}
