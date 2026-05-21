<?php

use App\Telegram\Handlers\AudioHandler;
use App\Telegram\Handlers\ChatRouter;
use App\Telegram\Handlers\TextHandler;

test('telegram message handlers can be resolved', function () {
    expect(app(ChatRouter::class))->toBeInstanceOf(ChatRouter::class)
        ->and(app(TextHandler::class))->toBeInstanceOf(TextHandler::class)
        ->and(app(AudioHandler::class))->toBeInstanceOf(AudioHandler::class);
});

test('telegram message router is registered with nutgram', function () {
    $this->artisan('nutgram:list')
        ->assertSuccessful();

    expect(file_get_contents(base_path('routes/telegram.php')))
        ->toContain(ChatRouter::class);
});
