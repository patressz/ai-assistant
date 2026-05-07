<?php

declare(strict_types=1);

namespace App\Neuron\Agents;

use NeuronAI\Agent\Agent;
use NeuronAI\Agent\SystemPrompt;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\OpenAI;
use NeuronAI\Tools\ToolInterface;
use NeuronAI\Tools\Toolkits\ToolkitInterface;

class Assistant extends Agent
{
    protected function provider(): AIProviderInterface
    {
        return new OpenAI(
            key: config('neuron.provider.openai.key'),
            model: config('neuron.provider.openai.model'),
            parameters: config('neuron.provider.openai.parameters'),
        );
    }

    public function instructions(): string
    {
        return (string) new SystemPrompt(
            ...config('neuron.system_prompt')
        );
    }

    /**
     * @return ToolInterface[]|ToolkitInterface[]
     */
    protected function tools(): array
    {
        return [];
    }
}
