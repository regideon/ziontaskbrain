<?php
/**
 * Laravel AI Agents (laravel/ai)
 * 
 * Agents are the key concept in laravel/ai. Each agent is a dedicated PHP class that encapsulates instructions, tools, and optionally conversation memory and structured output. You create them with php artisan make:agent AgentName.
 * 
 * The agent handles the tool-calling loop automatically. You call ->prompt() once and the SDK handles: call the model → model calls tool → run tool → send result back → repeat until 'stop' → return final response. No while loop needed.
 * 
 * Usage from a Livewire component — one line, no manual loop: 
 // The SDK automatically runs: list_tasks → prioritize_tasks → returns summary
 $summary = (new TaskPriorizerAgent(auth()->id()))->prompt('Prioritize my tasks now.');
 */
namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

use App\Ai\Tools\{ListTasksTool, PrioritizeTasksTool};
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Enums\Lab;


// Configure the agent with PHP attributes
#[Provider(Lab::OpenAI)]
#[Model('gpt-5.2')]
#[MaxSteps(8)]  // Max tool call iterations before stopping
class TaskPriorizerAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(private int $userId) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a productivity AI for ZionTaskBrain. When asked to prioritize tasks:' .
               ' 1) Call list_tasks to get pending tasks for user_id ' . $this->userId . '.' .
               ' 2) Analyze urgency based on due dates, priority labels, and task descriptions.' .
               ' 3) Call prioritize_tasks with a score (1-100) and brief reasoning for each task.' .
               ' 4) Return a 2-sentence summary of what you scored and why.';
    }


    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * The tools this agent can call. The SDK handles the loop automatically.
     */
    public function tools(): iterable
    {
        return [
            new ListTasksTool,
            new PrioritizeTasksTool,
        ];
    }

}
