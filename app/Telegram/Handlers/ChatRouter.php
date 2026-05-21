<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\MessageType;

class ChatRouter
{
    public function __construct(
        private TextHandler $textHandler,
        private AudioHandler $audioHandler,
    ) {}

    public function __invoke(Nutgram $bot): void
    {
        $message = $bot->message();

        if ($message === null) {
            return;
        }

        match ($message->getType()) {
            MessageType::TEXT => $this->textHandler->handle($message->chat->id, $message->getText()),
            MessageType::VOICE => $this->audioHandler->handle($bot, $message),
            default => $bot->sendMessage('Tento typ spravy zatial neviem spracovat.'),
        };
    }
}
