<?php

namespace App\Telegram\Handlers;

use App\Services\SpeechToText\OpenAiAudioTranscriber;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Media\File;
use SergiX44\Nutgram\Telegram\Types\Message\Message;
use Throwable;

class AudioHandler
{
    public function __construct(
        private OpenAiAudioTranscriber $audioTranscriber,
        private TextHandler $textHandler,
    ) {}

    public function handle(Nutgram $bot, Message $message): void
    {
        $chatId = $message->chat->id;
        $fileId = $message->voice?->file_id;

        if ($fileId === null) {
            $bot->sendMessage('Hlasovu spravu sa mi nepodarilo spracovat.');

            return;
        }

        defer(fn () => $this->respond($bot, $chatId, $fileId));
    }

    public function respond(Nutgram $bot, int|string $chatId, string $fileId): void
    {
        $audioStoragePath = null;

        try {
            $audioStoragePath = $this->downloadVoiceMessage($bot, $fileId);

            $transcript = $this->audioTranscriber->transcribe(
                Storage::disk('local')->path($audioStoragePath)
            );
        } catch (Throwable $exception) {
            report($exception);

            app(Nutgram::class)->sendMessage(
                text: 'Hlasovu spravu sa mi nepodarilo spracovat.',
                chat_id: $chatId,
            );

            return;
        } finally {
            if ($audioStoragePath !== null && Storage::disk('local')->exists($audioStoragePath)) {
                Storage::disk('local')->delete($audioStoragePath);
            }
        }

        $this->textHandler->respond($chatId, $transcript);
    }

    private function downloadVoiceMessage(Nutgram $bot, string $fileId): string
    {
        /** @var File|null $file */
        $file = $bot->getFile($fileId);

        if ($file === null) {
            throw new RuntimeException('Telegram voice file could not be resolved.');
        }

        $audioStoragePath = 'telegram/voice/'.Str::uuid().'.ogg';
        $audioPath = Storage::disk('local')->path($audioStoragePath);

        if ($file->save($audioPath) !== true || ! is_file($audioPath)) {
            throw new RuntimeException('Telegram voice file could not be downloaded.');
        }

        return $audioStoragePath;
    }
}
