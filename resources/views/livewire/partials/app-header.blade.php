@php
    $active = $active ?? 'tasks';
    $subtitle = $subtitle ?? 'Task Board';
@endphp

<header class="sticky top-0 z-30 border-b border-slate-100 bg-white p-4 dark:border-slate-800 dark:bg-[#101c22]">
    <div class="mx-auto flex w-full max-w-6xl items-center gap-3">
        <div class="flex size-10 items-center justify-center rounded-full bg-sky-500/10 text-sky-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6M12 9v6m8 1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h2l1-2h6l1 2h2a2 2 0 0 1 2 2v8Z"/>
            </svg>
        </div>
        <div class="flex-1">
            <h1 class="text-lg font-bold tracking-tight">ZionTaskBrain</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $subtitle }}</p>
        </div>
        {{--
        <a href="{{ route('home') }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold dark:border-slate-700">Home</a>
        <a href="{{ route('task-board') }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $active === 'tasks' ? 'bg-sky-500 text-white' : 'border border-slate-200 dark:border-slate-700' }}">Tasks</a>
        <a href="{{ route('agents-lab') }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $active === 'agents' ? 'bg-sky-500 text-white' : 'border border-slate-200 dark:border-slate-700' }}">Agents</a>
        <a href="{{ route('mcp-playground') }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ $active === 'mcp' ? 'bg-sky-500 text-white' : 'border border-slate-200 dark:border-slate-700' }}">MCP</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-semibold text-rose-700 dark:border-rose-900 dark:text-rose-300">Logout</button>
        </form>
        --}}
    </div>
</header>
