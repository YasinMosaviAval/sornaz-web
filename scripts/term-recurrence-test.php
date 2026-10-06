<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

$service = (new ReflectionClass(\Modules\Academy\Services\AcademyTermService::class))->newInstanceWithoutConstructor();
$jalali = new ReflectionMethod($service, 'jalaliParts');
$matches = new ReflectionMethod($service, 'matchesDraftRecurrence');
$validate = new ReflectionMethod($service, 'validateDraftRecurrence');
$dates = new ReflectionMethod($service, 'draftedDates');

foreach (['2025-03-21' => [1404, 1, 1], '2026-03-21' => [1405, 1, 1], '2026-09-22' => [1405, 6, 31]] as $gregorian => $expected) {
    if ($jalali->invoke($service, new DateTimeImmutable($gregorian)) !== $expected) {
        throw new RuntimeException("Incorrect Jalali date for {$gregorian}");
    }
}

$samples = [
    ['2026-03-21', 'week', ['weekday' => '6'], true],
    ['2026-03-22', 'week', ['weekday' => '6'], false],
    ['2026-03-21', 'month', ['day' => '1'], true],
    ['2026-04-22', 'month', ['day' => '1'], false],
    ['2026-03-21', 'year', ['month' => '1', 'day' => '1'], true],
    ['2026-03-21', 'year', ['month' => '2', 'day' => '1'], false],
    [(new DateTimeImmutable('+30 days'))->format('Y-m-d'), 'no-period', ['exactDate' => (new DateTimeImmutable('+30 days'))->format('Y-m-d')], true],
];
foreach ($samples as [$date, $period, $rule, $expected]) {
    $validate->invoke($service, $period, $rule);
    if ($matches->invoke($service, new DateTimeImmutable($date), $period, $rule) !== $expected) {
        throw new RuntimeException("Incorrect recurrence match for {$period} on {$date}");
    }
}

if ($dates->invoke($service, new DateTimeImmutable('2026-03-21'), 3, '2-week', ['weekday' => '6']) !== ['2026-03-21', '2026-04-04', '2026-04-18']) {
    throw new RuntimeException('Two-week session dates are incorrect.');
}
if ($dates->invoke($service, new DateTimeImmutable('2026-03-21'), 3, 'month', ['day' => '1']) !== ['2026-03-21', '2026-04-21', '2026-05-22']) {
    throw new RuntimeException('Jalali monthly session dates are incorrect.');
}
if ($dates->invoke($service, new DateTimeImmutable('2026-03-21'), 2, 'year', ['month' => '1', 'day' => '1']) !== ['2026-03-21', '2027-03-21']) {
    throw new RuntimeException('Jalali yearly session dates are incorrect.');
}

echo "Term recurrence checks passed.\n";
