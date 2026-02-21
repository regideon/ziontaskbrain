<?php
/**
 * Usage — forUser() enables conversation memory stored automatically in DB:
// Start a new conversation
\$response = (new DailySummaryAgent(\$userId))->forUser(\$user)->prompt('Give me my daily summary.');
\$conversationId = \$response->conversationId;  // store this for follow-ups


// Continue the conversation
\$response = (new DailySummaryAgent(\$userId))
    ->continue(\$conversationId, as: \$user)
    ->prompt('What should I focus on first?');

 */
namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

use App\Ai\Tools\GetStatsTool;
use App\Ai\Tools\ListTasksTool;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Enums\Lab;


#[Provider(Lab::OpenAI)]
#[Model('gpt-5.2')]
class DailySummaryAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(private int $userId) {}

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a productivity coach for ZionTaskAI. ' .
            'Always call get_stats first for user_id ' . $this->userId . '. ' .
            'Then call list_tasks with user_id ' . $this->userId . ', status pending, limit 20, ' .
            'and include actual task titles in your answer. ' .
            'Return: 1) one-line progress summary, 2) top 3 priorities with exact task names and short reason, ' .
            '3) one tactical suggestion that names the first task to start now.';
    }

    /**
     * Get the list of messages comprising the conversation so far.
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new GetStatsTool,
            new ListTasksTool,
        ];
    }
}
