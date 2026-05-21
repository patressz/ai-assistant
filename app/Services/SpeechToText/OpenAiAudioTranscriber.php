<?php

namespace App\Services\SpeechToText;

use NeuronAI\Chat\Enums\SourceType;
use NeuronAI\Chat\Messages\ContentBlocks\AudioContent;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\AIProviderInterface;
use NeuronAI\Providers\OpenAI\Audio\OpenAISpeechToText;
use RuntimeException;

class OpenAiAudioTranscriber
{
    public function transcribe(string $audioPath): string
    {
        if (! is_file($audioPath)) {
            throw new RuntimeException("Audio file [{$audioPath}] does not exist.");
        }

        $message = $this->provider()->chat(
            new UserMessage(
                new AudioContent($audioPath, SourceType::URL)
            )
        );

        $text = $message->getContent();

        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('OpenAI did not return a transcription.');
        }

        return trim($text);
    }

    protected function provider(): AIProviderInterface
    {
        $apiKey = (string) config('neuron.provider.openai-stt.key');

        if ($apiKey === '') {
            throw new RuntimeException('OpenAI API key is not configured.');
        }

        return new OpenAISpeechToText(
            key: $apiKey,
            model: (string) config('neuron.provider.openai-stt.model', 'gpt-4o-mini-transcribe'),
            language: (string) config('neuron.provider.openai-stt.language', 'sk'),
            parameters: config('neuron.provider.openai-stt.parameters', []),
        );
    }
}
