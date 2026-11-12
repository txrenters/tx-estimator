<?php

use App\Support\Pricing;

$cases = json_decode(file_get_contents(__DIR__.'/../fixtures/pricing-cases.json'), true);

test('pricing matches the original calculation', function (array $case) {
    $result = Pricing::calculate($case['lines'], $case['pricing']);

    foreach (['cost', 'subtotal', 'discount', 'total'] as $key) {
        expect($result[$key])->toEqualWithDelta((float) $case['expected'][$key], 0.0000001);
    }
    expect($result['incomplete'])->toBe($case['expected']['incomplete'])
        ->and($result['margin'])->toEqualWithDelta((float) $case['expected']['margin'], 0.000001);

    expect(Pricing::flags($result, $case['pricing']))->toBe($case['flags']);
})->with(collect($cases)->mapWithKeys(fn ($c) => [$c['name'] => [$c]])->all());
