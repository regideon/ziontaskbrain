<x-layouts.guest-main :title="'ZionTaskAI Documentation'">
    @php
        $agents = [
            [
                'name' => 'TaskCreatorAgent',
                'path' => 'app/Ai/Agents/TaskCreatorAgent.php',
                'purpose' => 'Turns plain user requests into structured task data, then calls the create tool to save a task.',
                'uses' => ['CreateTaskTool'],
                'returns' => 'Structured output: task_id, title, priority, confirmation_msg.',
                'example' => '"Tomorrow 9am call investor, high priority" -> creates a task automatically.',
            ],
            [
                'name' => 'DailySummaryAgent',
                'path' => 'app/Ai/Agents/DailySummaryAgent.php',
                'purpose' => 'Acts as a productivity coach by fetching real stats first, then generating a daily summary with priorities.',
                'uses' => ['GetStatsTool'],
                'returns' => 'Natural language summary with progress, top priorities, and tactical suggestion.',
                'example' => '"Give me my daily summary" -> returns current progress and recommended next actions.',
            ],
            [
                'name' => 'TaskPriorizerAgent',
                'path' => 'app/Ai/Agents/TaskPriorizerAgent.php',
                'purpose' => 'Prioritizes pending tasks by calling list first, then writing AI scores and reasoning back to the database.',
                'uses' => ['ListTasksTool', 'PrioritizeTasksTool'],
                'returns' => 'Two-sentence explanation of what was scored and why.',
                'example' => '"Prioritize my tasks now" -> updates ai_priority_score for tasks.',
            ],
        ];

        $aiTools = [
            [
                'name' => 'ListTasksTool',
                'path' => 'app/Ai/Tools/ListTasksTool.php',
                'purpose' => 'Reads tasks for a user with optional filters (status, priority, overdue, limit).',
                'input' => 'user_id (required), status, priority, overdue_only, limit.',
                'output' => 'JSON with total + task list (id, title, status, due_date, ai_priority_score, tags).',
            ],
            [
                'name' => 'CreateTaskTool',
                'path' => 'app/Ai/Tools/CreateTaskTool.php',
                'purpose' => 'Creates a task from structured data parsed by the model.',
                'input' => 'user_id, title, description, priority, due_date, category_id, tags.',
                'output' => 'JSON success response with task_id and title.',
            ],
            [
                'name' => 'CompleteTaskTool',
                'path' => 'app/Ai/Tools/CompleteTaskTool.php',
                'purpose' => 'Marks one task as completed by task_id.',
                'input' => 'task_id.',
                'output' => 'Success or error response text.',
            ],
            [
                'name' => 'GetStatsTool',
                'path' => 'app/Ai/Tools/GetStatsTool.php',
                'purpose' => 'Calculates live productivity counters.',
                'input' => 'user_id.',
                'output' => 'JSON stats: total, pending, in_progress, completed, overdue, ai_scored.',
            ],
            [
                'name' => 'PrioritizeTasksTool',
                'path' => 'app/Ai/Tools/PrioritizeTasksTool.php',
                'purpose' => 'Saves AI scores (1-100) and reasoning for many tasks.',
                'input' => 'priority_scores[] with task_id, score, reasoning.',
                'output' => 'JSON with success and tasks_updated count.',
            ],
        ];

        $mcpTools = [
            [
                'name' => 'ListTasksMcpTool',
                'path' => 'app/Mcp/Tools/ListTasksMcpTool.php',
                'purpose' => 'MCP endpoint for listing tasks with filtering and ownership checks.',
                'input' => 'user_id (optional), status, priority, overdue_only, limit.',
                'output' => 'JSON response with tasks payload.',
            ],
            [
                'name' => 'CreateTaskMcpTool',
                'path' => 'app/Mcp/Tools/CreateTaskMcpTool.php',
                'purpose' => 'MCP endpoint for creating tasks securely for the authenticated user.',
                'input' => 'title required; plus optional description, priority, due_date, category_id, tags.',
                'output' => 'JSON response with created task details.',
            ],
            [
                'name' => 'CompleteTaskMcpTool',
                'path' => 'app/Mcp/Tools/CompleteTaskMcpTool.php',
                'purpose' => 'MCP endpoint for completing one task if it belongs to the user.',
                'input' => 'task_id required, user_id optional.',
                'output' => 'JSON success with completed timestamp or error.',
            ],
            [
                'name' => 'PrioritizeTasksMcpTool',
                'path' => 'app/Mcp/Tools/PrioritizeTasksMcpTool.php',
                'purpose' => 'MCP endpoint that stores AI priority scores in bulk.',
                'input' => 'priority_scores[] required with task_id, score(1-100), reasoning optional.',
                'output' => 'JSON with tasks_updated and tasks_skipped.',
            ],
            [
                'name' => 'GetStatsMcpTool',
                'path' => 'app/Mcp/Tools/GetStatsMcpTool.php',
                'purpose' => 'MCP endpoint for stats used by dashboards, summaries, and assistants.',
                'input' => 'user_id optional.',
                'output' => 'JSON stats summary.',
            ],
        ];
    @endphp

    <div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0f1b20] dark:text-slate-100">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-[#0f1b20]/95">
            <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-600 dark:text-cyan-300">ZionTaskAI</p>
                    <h1 class="text-lg font-bold sm:text-xl">Project Documentation</h1>
                </div>
                <div class="flex items-center gap-2 text-xs sm:text-sm">
                    <a href="{{ route('home') }}" class="rounded-lg border border-slate-300 px-3 py-1.5 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800">Home</a>
                    @auth
                        <a href="{{ route('task-board') }}" class="rounded-lg bg-cyan-500 px-3 py-1.5 font-semibold text-white">Open App</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-cyan-500 px-3 py-1.5 font-semibold text-white">Login</a>
                    @endauth
                </div>
            </div>
        </header>

        <main class="mx-auto grid w-full max-w-7xl gap-6 px-4 py-6 sm:px-6 lg:grid-cols-[260px_1fr] lg:px-8">
            <aside class="lg:sticky lg:top-24 lg:h-[calc(100vh-7rem)]">
                <nav class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-400">On This Page</p>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#overview" class="text-cyan-700 hover:underline dark:text-cyan-300">Overview</a></li>
                        <li><a href="#architecture" class="text-cyan-700 hover:underline dark:text-cyan-300">Flow Architecture</a></li>
                        <li><a href="#ai-tools" class="text-cyan-700 hover:underline dark:text-cyan-300">AI Tools</a></li>
                        <li><a href="#agents" class="text-cyan-700 hover:underline dark:text-cyan-300">Agents</a></li>
                        <li><a href="#task-server" class="text-cyan-700 hover:underline dark:text-cyan-300">TaskServer</a></li>
                        <li><a href="#mcp-tools" class="text-cyan-700 hover:underline dark:text-cyan-300">MCP Tools</a></li>
                        <li><a href="#quick-test" class="text-cyan-700 hover:underline dark:text-cyan-300">Quick Test Script</a></li>
                        <li><a href="#debug-diagram" class="text-cyan-700 hover:underline dark:text-cyan-300">Debug Diagram</a></li>
                    </ul>
                </nav>
            </aside>

            <section class="space-y-6">
                <article id="overview" class="overflow-hidden rounded-2xl border border-cyan-200 bg-gradient-to-br from-cyan-500 to-sky-600 p-6 text-white shadow-lg shadow-cyan-500/20">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-100">Overview</p>
                    <h2 class="mt-2 text-2xl font-black tracking-tight sm:text-3xl">How ZionTaskAI Works</h2>
                    <p class="mt-3 max-w-3xl text-sm text-cyan-50 sm:text-base">
                        ZionTaskAI combines manual task management, AI-assisted agents, and MCP server tools.
                        The same task data is available in UI, agent workflows, and MCP API calls so everything stays synchronized.
                    </p>
                </article>

                <article id="architecture" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold">Flow Architecture</h3>
                    <div class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">1. UI Layer</p>
                            <p class="mt-1 text-sm">Livewire pages: Task Board, Agents Lab, MCP Playground.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">2. Logic Layer</p>
                            <p class="mt-1 text-sm">AI Agents call AI Tools. MCP Server routes JSON-RPC calls to MCP Tools.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-800/40">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">3. Data Layer</p>
                            <p class="mt-1 text-sm">All paths read/write the same <code>tasks</code> table through Eloquent.</p>
                        </div>
                    </div>
                </article>

                <article id="ai-tools" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold">AI Tools (<code>app/Ai/Tools</code>)</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">These tools are used by Agents through <code>laravel/ai</code> for model tool-calling.</p>
                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        @foreach ($aiTools as $tool)
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                <h4 class="text-base font-bold">{{ $tool['name'] }}</h4>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><code>{{ $tool['path'] }}</code></p>
                                <p class="mt-3 text-sm"><span class="font-semibold">Purpose:</span> {{ $tool['purpose'] }}</p>
                                <p class="mt-2 text-sm"><span class="font-semibold">Input:</span> {{ $tool['input'] }}</p>
                                <p class="mt-2 text-sm"><span class="font-semibold">Output:</span> {{ $tool['output'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article id="agents" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold">Agents (<code>app/Ai/Agents</code>)</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">All agents are configured with OpenAI <code>gpt-5.2</code>.</p>
                    <div class="mt-4 space-y-4">
                        @foreach ($agents as $agent)
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                <h4 class="text-base font-bold">{{ $agent['name'] }}</h4>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><code>{{ $agent['path'] }}</code></p>
                                <p class="mt-3 text-sm"><span class="font-semibold">Purpose:</span> {{ $agent['purpose'] }}</p>
                                <p class="mt-2 text-sm"><span class="font-semibold">Uses:</span> {{ implode(', ', $agent['uses']) }}</p>
                                <p class="mt-2 text-sm"><span class="font-semibold">Returns:</span> {{ $agent['returns'] }}</p>
                                <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-sm dark:bg-slate-800/60"><span class="font-semibold">Example:</span> {{ $agent['example'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article id="task-server" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold">MCP Server (<code>app/Mcp/Servers/TaskServer.php</code>)</h3>
                    <p class="mt-2 text-sm">
                        <code>TaskServer</code> is your MCP gateway. It defines server metadata (name/version/instructions)
                        and registers all available MCP tools that external clients can call via JSON-RPC.
                    </p>
                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-700 dark:bg-slate-800/40">
<pre class="overflow-x-auto text-xs sm:text-sm"><code>Registered tools:
- list_tasks
- create_task
- complete_task
- prioritize_tasks
- get_stats</code></pre>
                    </div>
                </article>

                <article id="mcp-tools" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold">MCP Tools (<code>app/Mcp/Tools</code>)</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">These are exposed by MCP and designed for secure, authenticated remote calls.</p>
                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        @foreach ($mcpTools as $tool)
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-700">
                                <h4 class="text-base font-bold">{{ $tool['name'] }}</h4>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><code>{{ $tool['path'] }}</code></p>
                                <p class="mt-3 text-sm"><span class="font-semibold">Purpose:</span> {{ $tool['purpose'] }}</p>
                                <p class="mt-2 text-sm"><span class="font-semibold">Input:</span> {{ $tool['input'] }}</p>
                                <p class="mt-2 text-sm"><span class="font-semibold">Output:</span> {{ $tool['output'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>

                <article id="quick-test" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm dark:border-emerald-900 dark:bg-emerald-900/20">
                    <h3 class="text-lg font-bold text-emerald-900 dark:text-emerald-100">Quick Test Script (MCP Playground)</h3>
                    <ol class="mt-3 list-decimal space-y-1 pl-5 text-sm text-emerald-900 dark:text-emerald-100">
                        <li>Open <code>/mcp-playground</code> and click <code>Initialize</code>.</li>
                        <li>Click <code>list_tasks</code> to verify read access.</li>
                        <li>Click <code>create_task demo</code> to insert one task.</li>
                        <li>Click <code>get_stats</code> and confirm total increased.</li>
                    </ol>
                </article>

                <article id="debug-diagram" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/50">
                    <h3 class="text-lg font-bold">Debug Diagram: Agent Trigger Flow</h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                        Use this when tracing what happens after clicking buttons in <code>/agents</code>.
                    </p>

                    <div class="mt-4 grid gap-4 lg:grid-cols-2">
                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 dark:border-emerald-900 dark:bg-emerald-900/20">
                            <h4 class="text-sm font-bold text-emerald-800 dark:text-emerald-200">TaskCreatorAgent</h4>
<pre class="mt-3 overflow-x-auto rounded-lg bg-white p-3 text-xs dark:bg-slate-900"><code>resources/views/livewire/ai-assistant.blade.php
(Create via Agent click)
        ->
app/Livewire/AiAssistant.php:createWithAgent()
(validate prompt + call agent)
        ->
app/Ai/Agents/TaskCreatorAgent.php
(LLM parses natural language to task fields)
        ->
app/Ai/Tools/CreateTaskTool.php:handle()
(insert task into tasks table)
        ->
AiAssistant::$creatorOutput
(render response in /agents UI)
        ->
dispatch('board-updated')
(task-board list refresh)</code></pre>
                        </div>

                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900 dark:bg-amber-900/20">
                            <h4 class="text-sm font-bold text-amber-800 dark:text-amber-200">DailySummaryAgent</h4>
<pre class="mt-3 overflow-x-auto rounded-lg bg-white p-3 text-xs dark:bg-slate-900"><code>resources/views/livewire/ai-assistant.blade.php
(Generate Summary click)
        ->
app/Livewire/AiAssistant.php:summarizeDay()
(validate prompt + call agent)
        ->
app/Ai/Agents/DailySummaryAgent.php
(instruction-guided tool calling)
        ->
app/Ai/Tools/GetStatsTool.php
(counts: total/pending/completed/overdue)
        ->
app/Ai/Tools/ListTasksTool.php
(reads real pending task titles)
        ->
AiAssistant::$summaryOutput
(render markdown summary in /agents UI)</code></pre>
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-700 dark:bg-slate-800/40">
                        <p class="font-semibold">Fallback Path in Summary:</p>
                        <p class="mt-1">
                            If AI returns empty/error, <code>AiAssistant::summarizeDay()</code> calls
                            <code>tryBuildFallbackSummary()</code> then <code>buildFallbackSummary()</code>
                            to generate output directly from the <code>tasks</code> table.
                        </p>
                    </div>
                </article>
            </section>
        </main>
    </div>
</x-layouts.guest-main>
