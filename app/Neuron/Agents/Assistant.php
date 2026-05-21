<?php

declare(strict_types=1);

namespace App\Neuron\Agents;

use App\Neuron\Agents\Tools\SendTelegramAudioMessageTool;
use App\Neuron\Agents\Tools\SendTelegramTextMessageTool;
use App\Services\TextToSpeech\OpenAiAudioGenerator;
use NeuronAI\Agent\Agent;
use NeuronAI\Agent\SystemPrompt;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\Responses\OpenAIResponses;
use NeuronAI\Tools\ProviderTool;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;
use NeuronAI\Workflow\Persistence\PersistenceInterface;
use NeuronAI\Workflow\WorkflowState;

class Assistant extends Agent
{
    public function __construct(
        private int|string $telegramChatId,
        ?PersistenceInterface $persistence = null,
        ?string $resumeToken = null,
        ?WorkflowState $state = null,
    ) {
        parent::__construct($persistence, $resumeToken, $state);
    }

    protected function provider(): AIProviderInterface
    {
        return new OpenAIResponses(
            key: config('neuron.provider.openai.key'),
            model: 'gpt-5.5',
            parameters: config('neuron.provider.openai.parameters'),
        );
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt([
            view('ai.prompts.system')->render(),
        ]);
    }

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    protected function tools(): array
    {
        return [
            new SendTelegramTextMessageTool($this->telegramChatId),
            new SendTelegramAudioMessageTool(
                chatId: $this->telegramChatId,
                audioGenerator: app(OpenAiAudioGenerator::class),
            ),
            ProviderTool::make(
                type: 'web_search'
            ),
        ];
    }
}
