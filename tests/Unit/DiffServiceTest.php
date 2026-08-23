<?php

use App\Services\DiffService;
use Illuminate\Support\Str;

it('produces delete and insert ops for changed lines', function () {
    $ops = (new DiffService)->lineDiff(
        "You are a helpful assistant.\nWrite a tagline.",
        "You are a witty assistant.\nWrite a tagline.\nKeep it under 8 words.",
    );

    expect($ops)->toContain(['op' => DiffService::OP_DELETE, 'text' => 'You are a helpful assistant.'])
        ->and($ops)->toContain(['op' => DiffService::OP_INSERT, 'text' => 'You are a witty assistant.'])
        ->and($ops)->toContain(['op' => DiffService::OP_INSERT, 'text' => 'Keep it under 8 words.'])
        ->and(collect($ops)->where('op', DiffService::OP_EQUAL)->pluck('text')->all())
        ->toBe(['Write a tagline.']);
});

it('handles identical and empty inputs without ops noise', function () {
    expect((new DiffService)->lineDiff('same', 'same'))
        ->toBe([['op' => DiffService::OP_EQUAL, 'text' => 'same']])
        ->and((new DiffService)->lineDiff('', ''))->toBe([])
        ->and((new DiffService)->lineDiff('', "brand new\nlines"))
        ->toBe([
            ['op' => DiffService::OP_INSERT, 'text' => 'brand new'],
            ['op' => DiffService::OP_INSERT, 'text' => 'lines'],
        ]);
});

it('stays fast on prompt-sized inputs', function () {
    $big = Str::of("line\n")->repeat(400)->append('final')->toString();
    $start = microtime(true);
    $ops = (new DiffService)->lineDiff($big, str_replace('line', 'LINE', $big));
    $elapsed = microtime(true) - $start;

    expect($elapsed)->toBeLessThan(2.0)
        ->and(count($ops))->toBeGreaterThan(0);
});
