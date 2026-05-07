<?php


use App\Telegram\Handlers\ChatHandler;
use SergiX44\Nutgram\Nutgram;

/** @var Nutgram $bot */

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

$bot->onText('(.*)', ChatHandler::class);
