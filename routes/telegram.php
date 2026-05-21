<?php

/** @var Nutgram $bot */

use App\Telegram\Handlers\ChatRouter;
use SergiX44\Nutgram\Nutgram;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

$bot->onCommand('start', function (Nutgram $bot) {
    $bot->sendMessage('Ahoj, som pripraveny.');
})->description('The start command!');

$bot->onMessage(ChatRouter::class);
