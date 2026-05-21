<?php

namespace App\Services\TextToSpeech;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use NeuronAI\Chat\Messages\UserMessage;
use NeuronAI\Providers\OpenAI\Audio\OpenAITextToSpeech;
use RuntimeException;

class OpenAiAudioGenerator
{
    public function generate(string $text): string
    {
        $message = $this->provider()->chat(new UserMessage($text));
        $audio = $message->getAudio();

        if ($audio === null) {
            throw new RuntimeException('Text to speech provider did not return audio.');
        }

        $decodedAudio = base64_decode($audio->getContent(), true);

        if ($decodedAudio === false) {
            throw new RuntimeException('Text to speech provider returned invalid audio.');
        }

        $path = 'telegram/tts/'.Str::uuid().'.mp3';

        if (Storage::disk('local')->put($path, $decodedAudio) !== true) {
            throw new RuntimeException('Generated audio could not be stored.');
        }

        return $path;
    }

    protected function provider(): OpenAITextToSpeech
    {
        $apiKey = (string) config('neuron.provider.openai-tts.key');
        $voice = (string) config('neuron.provider.openai-tts.voice');

        if ($apiKey === '' || $voice === '') {
            throw new RuntimeException('OpenAI TTS is not configured.');
        }

        return new OpenAITextToSpeech(
            key: $apiKey,
            model: (string) config('neuron.provider.openai-tts.model', 'gpt-4o-mini-tts'),
            voice: $voice,
            parameters: config('neuron.provider.openai-tts.parameters', []),
        );
    }
}
