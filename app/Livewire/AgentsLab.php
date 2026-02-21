<?php

namespace App\Livewire;

use App\Ai\Agents\TaskPriorizerAgent;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AgentsLab extends Component
{
    public int $userId = 0;
    public string $prioritizerSummary = '';
    public string $error = '';

    public function mount(): void
    {
        $userId = auth()->id();
        $this->userId = $userId ? (int) $userId : 0;
    }

    public function prioritizeWithAgent(): void
    {
        $this->prioritizerSummary = '';
        $this->error = '';

        try {
            $response = (new TaskPriorizerAgent($this->userId))
                ->prompt('Prioritize my pending tasks now and explain your ranking.');

            $this->prioritizerSummary = (string) $response;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
        }
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
        return view('livewire.agents-lab')
            ->layout('components.layouts.livewire-main', [
                'title' => 'ZionTaskBrain - Agents',
            ]);
    }
}
