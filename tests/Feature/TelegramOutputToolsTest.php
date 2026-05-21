<?php

use App\Neuron\Agents\Assistant;
use App\Neuron\Agents\Tools\SendTelegramAudioMessageTool;
use App\Neuron\Agents\Tools\SendTelegramTextMessageTool;
use App\Services\TextToSpeech\OpenAiAudioGenerator;

test('telegram output tools are registered with stable names', function () {
    $textTool = new SendTelegramTextMessageTool(123);
    $audioTool = new SendTelegramAudioMessageTool(123, app(OpenAiAudioGenerator::class));

    expect($textTool->getName())->toBe('send_text_message')
        ->and($audioTool->getName())->toBe('send_audio_message');
});

test('telegram output tools describe their required input', function () {
    $textProperties = collect((new SendTelegramTextMessageTool(123))->getProperties());
    $audioProperties = collect((new SendTelegramAudioMessageTool(123, app(OpenAiAudioGenerator::class)))->getProperties());

    expect($textProperties->first(fn ($property): bool => $property->getName() === 'text')?->isRequired())->toBeTrue()
        ->and($audioProperties->first(fn ($property): bool => $property->getName() === 'text')?->isRequired())->toBeTrue()
        ->and($audioProperties->first(fn ($property): bool => $property->getName() === 'caption')?->isRequired())->toBeFalse();
});

test('telegram assistant initializes workflow internals', function () {
    $assistant = Assistant::make(123);
    $executor = new ReflectionProperty($assistant, 'executor');

    expect($assistant)->toBeInstanceOf(Assistant::class)
        ->and($executor->isInitialized($assistant))->toBeTrue();
});
