<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ZionTaskBrain' }}</title>
    {{-- @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                    },
                },
            };
        </script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @endif --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        },
                    },
                },
            };
        </script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @livewireStyles
</head>
<body class="min-h-screen">
    <div class="pb-28">
        {{ $slot }}
    </div>

    @php
        $isTasks = request()->routeIs('task-board');
        $isAgents = request()->routeIs('agents-lab');
        $isMcp = request()->routeIs('mcp-playground');
    @endphp
    <nav
        class="border-t border-slate-200 bg-white/95 px-4 pt-2 backdrop-blur dark:border-slate-800 dark:bg-[#101c22]/95"
        style="position: fixed; left: 0; right: 0; bottom: 0; z-index: 9999; padding-bottom: calc(env(safe-area-inset-bottom) + 0.75rem); box-shadow: 0 -8px 20px rgba(15, 23, 42, 0.08);"
    >
        <div class="mx-auto grid w-full max-w-6xl grid-cols-4 items-end gap-1">
            <a href="{{ route('task-board') }}" class="flex flex-col items-center justify-center gap-1 rounded-lg py-1.5 {{ $isTasks ? 'text-cyan-600 dark:text-cyan-300' : 'text-slate-500 dark:text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3.75 4.5h16.5M3.75 10.5h16.5M3.75 16.5h16.5"/>
                </svg>
                <span class="text-[11px] font-semibold">Dashboard/Tasks</span>
            </a>
            <a href="{{ route('agents-lab') }}" class="flex flex-col items-center justify-center gap-1 rounded-lg py-1.5 {{ $isAgents ? 'text-cyan-600 dark:text-cyan-300' : 'text-slate-500 dark:text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 7h6m-6 4h6m-6 4h4M6.5 3.75h11A1.75 1.75 0 0 1 19.25 5.5v13A1.75 1.75 0 0 1 17.5 20.25h-11A1.75 1.75 0 0 1 4.75 18.5v-13A1.75 1.75 0 0 1 6.5 3.75Z"/>
                </svg>
                <span class="text-[11px] font-semibold">Agents</span>
            </a>
            <a href="{{ route('mcp-playground') }}" class="flex flex-col items-center justify-center gap-1 rounded-lg py-1.5 {{ $isMcp ? 'text-cyan-600 dark:text-cyan-300' : 'text-slate-500 dark:text-slate-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 8.5h10M7 12h10M7 15.5h6M5.75 4.75h12.5A1.75 1.75 0 0 1 20 6.5v11a1.75 1.75 0 0 1-1.75 1.75H5.75A1.75 1.75 0 0 1 4 17.5v-11A1.75 1.75 0 0 1 5.75 4.75Z"/>
                </svg>
                <span class="text-[11px] font-semibold">MCP</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex">
                @csrf
                <button type="submit" class="flex w-full flex-col items-center justify-center gap-1 rounded-lg py-1.5 text-slate-500 dark:text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 7.5V6A2.25 2.25 0 0 0 13.5 3.75h-7.5A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25h7.5A2.25 2.25 0 0 0 15.75 18v-1.5m-4.5-4.5h9m0 0-2.25-2.25M20.25 12l-2.25 2.25"/>
                    </svg>
                    <span class="text-[11px] font-semibold">Profile</span>
                </button>
            </form>
        </div>
    </nav>

    @livewireScripts
</body>
</html>
