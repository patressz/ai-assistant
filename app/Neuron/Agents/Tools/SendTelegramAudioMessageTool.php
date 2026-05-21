<?php

declare(strict_types=1);

namespace App\Neuron\Agents\Tools;

use App\Services\TextToSpeech\OpenAiAudioGenerator;
use Illuminate\Support\Facades\Storage;
use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Internal\InputFile;

class SendTelegramAudioMessageTool extends Tool
{
    public function __construct(
        private int|string $chatId,
        private OpenAiAudioGenerator $audioGenerator,
    ) {
        parent::__construct(
            'send_audio_message',
            'Convert text to speech and send it as an audio message to the user in the current Telegram conversation.',
        );

    }

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'text',
                type: PropertyType::STRING,
                description: 'The exact text to synthesize and send as audio.',
                required: true,
            ),
            new ToolProperty(
                name: 'caption',
                type: PropertyType::STRING,
                description: 'Optional short caption for the audio message.',
                required: false,
            ),
        ];
    }

    public function __invoke(string $text, ?string $caption = null): string
    {
        info('inside send telegram audio message tool invoke method');
        $audioPath = $this->audioGenerator->generate($text);
        $absolutePath = Storage::disk('local')->path($audioPath);

        try {
            app(Nutgram::class)->sendAudio(
                audio: InputFile::make($absolutePath),
                chat_id: $this->chatId,
                caption: $caption,
            );
        } finally {
            if (Storage::disk('local')->exists($audioPath)) {
                Storage::disk('local')->delete($audioPath);
            }
        }

        return 'Audio message sent.';
    }
}
