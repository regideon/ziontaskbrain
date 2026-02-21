<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\CompleteTaskMcpTool;
use App\Mcp\Tools\CreateTaskMcpTool;
use App\Mcp\Tools\GetStatsMcpTool;
use App\Mcp\Tools\ListTasksMcpTool;
use App\Mcp\Tools\PrioritizeTasksMcpTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Task Server')]
#[Version('1.0.0')]
#[Instructions(
    'Task management server for ZionTaskBrain. ' .
    'Use list_tasks before prioritize_tasks and use get_stats before summary workflows. ' .
    'If user_id is omitted, tools default to authenticated user id.'
)]
class TaskServer extends Server
{
    protected array $tools = [
        ListTasksMcpTool::class,
        CreateTaskMcpTool::class,
        CompleteTaskMcpTool::class,
        PrioritizeTasksMcpTool::class,
        GetStatsMcpTool::class,
    ];

    protected array $resources = [
        //
    ];

    protected array $prompts = [
        //
    ];
}
