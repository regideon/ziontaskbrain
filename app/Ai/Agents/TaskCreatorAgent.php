<?php
/**
 * Laravel AI Agents (laravel/ai)
 * 
 * Usage — structured output returns an array-like response:
$response = (new TaskCreatorAgent(auth()->id()))->prompt($userInput);
$taskId = $response['task_id'];   // typed, validated structured output
$msg    = $response['confirmation_msg'];
 */
namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Promptable;
use Stringable;

use App\Ai\Tools\CreateTaskTool;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Enums\Lab;


#[Provider(Lab::OpenAI)]
#[Model('gpt-5.2')]
class TaskCreatorAgent implements Agent, Conversational, HasTools, HasStructuredOutput
{
    use Promptable;

    public function __construct(private int $userId) {}


    public function instructions(): Stringable|string
    {
        return 'Parse the user\'s description into a task and call create_task to save it. ' .
               'Today is ' . now()->toDateString() . '. user_id is ' . $this->userId . '. ' .
               'Extract: title, description, priority (low/medium/high/urgent), due_date (YYYY-MM-DD or null), tags.';
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
        return [ new CreateTaskTool ];
    }


    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'task_id'         => $schema->integer()->required(),
            'title'           => $schema->string()->required(),
            'priority'        => $schema->string()->required(),
            'confirmation_msg'=> $schema->string()->required(),
        ];

    }
}
