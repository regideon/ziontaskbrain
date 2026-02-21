<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class McpPlayground extends Component
{
    public int $userId = 0;

    public function mount(): void
    {
        $userId = auth()->id();
        $this->userId = $userId ? (int) $userId : 0;
    }

    #[Computed]
    public function users(): Collection
    {
        if ($this->userId > 0) {
            return User::query()->whereKey($this->userId)->get(['id', 'name', 'email']);
        }

        return User::query()->whereRaw('1 = 0')->get(['id', 'name', 'email']);
    }

    public function render()
    {
        return view('livewire.mcp-playground')
            ->layout('components.layouts.livewire-main', [
                'title' => 'ZionTaskBrain - MCP Playground',
            ]);
    }
}
