<?php

declare(strict_types=1);

namespace App\Neuron\Agents\Tools;

use NeuronAI\Tools\PropertyType;
use NeuronAI\Tools\Tool;
use NeuronAI\Tools\ToolProperty;
use SergiX44\Nutgram\Nutgram;

class SendTelegramTextMessageTool extends Tool
{
    public function __construct(
        private int|string $chatId,
    ) {
        parent::__construct(
            'send_text_message',
            'Send a text message to the user in the current Telegram conversation.',
        );

    }

    protected function properties(): array
    {
        return [
            new ToolProperty(
                name: 'text',
                type: PropertyType::STRING,
                description: 'The exact text to send to the user.',
                required: true,
            ),
        ];
    }

    public function __invoke(string $text): string
    {
        info('inside send telegram text message tool invoke method');
        app(Nutgram::class)->sendMessage(
            text: $text,
            chat_id: $this->chatId,
        );

        return 'Text message sent.';
    }
}
