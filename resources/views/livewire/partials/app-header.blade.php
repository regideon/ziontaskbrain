@php
    $active = $active ?? 'tasks';
    $subtitle = $subtitle ?? 'Task Board';
@endphp

<header class="sticky top-0 z-30 border-b border-slate-100 bg-white p-4 dark:border-slate-800 dark:bg-[#101c22]">
    <div class="mx-auto flex w-full max-w-6xl items-center gap-3">
        <div class="flex size-16 items-center justify-center rounded-full bg-blue-600 text-xl font-black text-white shadow-md shadow-blue-600/25">
            ZT
        </div>
        <div class="flex-1">
            <p class="text-[11px] font-bold uppercase tracking-[0.3em] text-slate-500 dark:text-slate-400">ZionTaskAI</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-700 dark:text-slate-200">{{ $subtitle }}</h1>
        </div>
        <button type="button" class="relative flex size-14 items-center justify-center rounded-2xl border border-slate-200 bg-slate-50 text-slate-600 shadow-sm dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200">
            <span class="absolute -right-1 -top-1 flex size-6 items-center justify-center rounded-full bg-rose-500 text-xs font-bold text-white">3</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4a2 2 0 0 1-.6-1.4V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0"/>
            </svg>
        </button>
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
