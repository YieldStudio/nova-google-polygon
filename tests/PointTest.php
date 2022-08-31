<?php

use YieldStudio\NovaGooglePolygon\Exceptions\InvalidPoint;
use YieldStudio\NovaGooglePolygon\Support\Point;

it('create point', function () {
    expect(new Point(1, 1))
        ->toBeInstanceOf(Point::class)
        ->and(Point::fromArray([1, 1]))
        ->toBeInstanceOf(Point::class)
        ->and(Point::fromArray(['lat' => 1, 'lng' => 1]))
        ->toBeInstanceOf(Point::class);
});

it('invalid point throws an exception', function ($data) {
    Point::fromArray($data);
})->with([
    [[1, 'Test']],
    [['lat' => 1, 'lng' => 'Test']],
    [['lat' => 1]],
    [[0]],
])->throws(InvalidPoint::class);
