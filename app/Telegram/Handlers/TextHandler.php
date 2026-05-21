<?php

namespace App\Telegram\Handlers;

use App\Neuron\Agents\Assistant;
use NeuronAI\Chat\Messages\UserMessage;
use SergiX44\Nutgram\Nutgram;
use Throwable;

class TextHandler
{
    public function handle(int|string $chatId, string $message): void
    {
        defer(fn () => $this->respond($chatId, $message));
    }

    public function respond(int|string $chatId, string $message): void
    {
        try {
            info('inside text handler respond method');
            Assistant::make($chatId)->chat(
                new UserMessage($message)
            )->getMessage();
        } catch (Throwable $exception) {
            report($exception);

            app(Nutgram::class)->sendMessage(
                text: 'Nieco sa pokazilo pri generovani odpovede. Skus mi to prosim poslat este raz.',
                chat_id: $chatId,
            );

            return;
        }
    }
}
