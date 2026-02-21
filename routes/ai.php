<?php

use Laravel\Mcp\Facades\Mcp;
use App\Mcp\Servers\TaskServer;

// Mcp::web('/mcp/demo', \App\Mcp\Servers\PublicServer::class);

// Web — HTTP POST for remote AI clients (Claude, ChatGPT plugins...)
Mcp::web('/mcp/tasks', TaskServer::class)
    ->middleware(['throttle:mcp']);


// Local — Artisan command for Claude Desktop / Laravel Boost
// Mcp::local('tasks', TaskServer::class);
