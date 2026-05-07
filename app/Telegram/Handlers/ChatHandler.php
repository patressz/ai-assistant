<?php

namespace App\Telegram\Handlers;

use SergiX44\Nutgram\Nutgram;

class ChatHandler
{
    public function __invoke(Nutgram $bot, string $message): void
    {
        info("Received message: {$message}");
        // $bot->sendMessage("Prijal som spravu: {$message}");
    }
}
