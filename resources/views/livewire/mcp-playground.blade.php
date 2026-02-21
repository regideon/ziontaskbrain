<div class="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#101f22] dark:text-slate-100">
    @include('livewire.partials.app-header', ['active' => 'mcp', 'subtitle' => 'MCP Playground Module'])

    <div class="mx-auto w-full max-w-6xl px-4 py-6">
        <article class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#1a2b2e]">
            <h2 class="mb-2 text-sm font-bold uppercase tracking-[0.14em] text-slate-600 dark:text-slate-300">JSON-RPC Controls</h2>
            <p class="mb-4 text-sm text-slate-500 dark:text-slate-400">Use these buttons to call your real MCP endpoint: <code>/mcp/tasks</code>.</p>

            <div class="mb-3">
                <label for="user" class="mb-1 block text-xs font-semibold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">Active User</label>
                <select id="user" wire:model.live="userId" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-600 dark:bg-[#101f22]">
                    @foreach ($this->users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button" data-mcp-action class="inline-flex items-center gap-2 rounded-lg bg-cyan-500 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60" onclick="window.mcpPlayground.init(this)">
                    <span class="mcp-label">Initialize</span>
                </button>
                <button type="button" data-mcp-action class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60 dark:bg-slate-700" onclick="window.mcpPlayground.call(this, 'list_tasks', { user_id: Number(@js($userId)) })">
                    <span class="mcp-label">list_tasks</span>
                </button>
                <button type="button" data-mcp-action class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60 dark:bg-slate-700" onclick="window.mcpPlayground.call(this, 'get_stats', { user_id: Number(@js($userId)) })">
                    <span class="mcp-label">get_stats</span>
                </button>
                <button type="button" data-mcp-action class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-60" onclick="window.mcpPlayground.createDemoTask(this, Number(@js($userId)))">
                    <span class="mcp-label">create_task demo</span>
                </button>
            </div>

            <p id="mcp-status" class="mt-3 text-xs text-slate-500 dark:text-slate-400">Session: not initialized</p>
            <pre id="mcp-output" class="mt-3 max-h-[28rem] overflow-auto rounded-lg bg-slate-100 p-3 text-xs whitespace-pre-wrap dark:bg-[#101f22]">No MCP calls yet.</pre>
        </article>
    </div>

    <script>
        (() => {
            let sessionId = null;
            let nextId = 10;
            const out = () => document.getElementById('mcp-output');
            const status = () => document.getElementById('mcp-status');
            const actionButtons = () => Array.from(document.querySelectorAll('[data-mcp-action]'));

            const write = (payload) => {
                out().textContent = JSON.stringify(payload, null, 2);
                status().textContent = sessionId ? `Session: ${sessionId}` : 'Session: not initialized';
            };

            const setButtonsLoading = (isLoading, activeButton = null) => {
                actionButtons().forEach((button) => {
                    const label = button.querySelector('.mcp-label');
                    if (!label) return;

                    if (isLoading) {
                        button.disabled = true;
                        if (button === activeButton) {
                            label.dataset.original = label.textContent;
                            label.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="size-3.5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0a12 12 0 00-12 12h4z"></path></svg>Processing...</span>';
                        }
                        return;
                    }

                    button.disabled = false;
                    if (button === activeButton && label.dataset.original) {
                        label.textContent = label.dataset.original;
                        delete label.dataset.original;
                    }
                });
            };

            const request = async (body, withSession = true) => {
                const headers = {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                };

                if (withSession && sessionId) {
                    headers['MCP-Session-Id'] = sessionId;
                }

                const response = await fetch('/mcp/tasks', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(body),
                    credentials: 'same-origin',
                });

                const raw = await response.text();
                let parsedBody;

                try {
                    parsedBody = JSON.parse(raw);
                } catch {
                    parsedBody = {
                        parse_error: 'Response is not JSON',
                        raw_preview: raw.slice(0, 1200),
                    };
                }

                if (response.headers.get('MCP-Session-Id')) {
                    sessionId = response.headers.get('MCP-Session-Id');
                }

                return {
                    status: response.status,
                    ok: response.ok,
                    headers: {
                        sessionId,
                        contentType: response.headers.get('Content-Type'),
                    },
                    body: parsedBody,
                };
            };

            window.mcpPlayground = {
                async init(button) {
                    setButtonsLoading(true, button);
                    try {
                        const result = await request({
                            jsonrpc: '2.0',
                            id: nextId++,
                            method: 'initialize',
                            params: {
                                protocolVersion: '2025-06-18',
                                capabilities: {},
                                clientInfo: { name: 'mcp-playground-ui', version: '1.0.0' },
                            },
                        }, false);
                        write(result);
                    } catch (error) {
                        write({ error: String(error) });
                    } finally {
                        setButtonsLoading(false, button);
                    }
                },
                async call(button, name, argumentsPayload) {
                    setButtonsLoading(true, button);
                    try {
                        const result = await request({
                            jsonrpc: '2.0',
                            id: nextId++,
                            method: 'tools/call',
                            params: {
                                name,
                                arguments: argumentsPayload,
                            },
                        });
                        write(result);
                    } catch (error) {
                        write({ error: String(error) });
                    } finally {
                        setButtonsLoading(false, button);
                    }
                },
                async createDemoTask(button, userId) {
                    setButtonsLoading(true, button);
                    try {
                        const result = await request({
                            jsonrpc: '2.0',
                            id: nextId++,
                            method: 'tools/call',
                            params: {
                                name: 'create_task',
                                arguments: {
                                    user_id: userId,
                                    title: `MCP demo task ${Math.floor(Math.random() * 1000)}`,
                                    priority: 'medium',
                                },
                            },
                        });
                        write(result);
                    } catch (error) {
                        write({ error: String(error) });
                    } finally {
                        setButtonsLoading(false, button);
                    }
                },
            };
        })();
    </script>
</div>
