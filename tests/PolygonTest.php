<?php

use YieldStudio\NovaGooglePolygon\Exceptions\InvalidPoint;
use YieldStudio\NovaGooglePolygon\Support\Polygon;

it('point on vertex', function () {
    $polygon = new Polygon([
        [0, 0],
        [0, 1],
    ]);

    expect($polygon)
        ->pointOnVertex([0, 0])->toBeTrue()
        ->pointOnVertex([5, 5])->toBeFalse()
        ->pointOnVertex([0, 0.5])->toBeFalse();
});


it('point in polygon', function () {
    $polygon = new Polygon([
        [1, 1],
        [1, 2],
        [3, 2],
        [3, 1],
    ]);

    expect($polygon)
        ->contain([0,0])->toBeFalse()
        ->contain([2,5])->toBeFalse()
        ->contain([1,1])->toBeTrue()
        ->contain([2.5, 1.5])->toBeTrue();
});


it('get bounding box', function () {
    $polygon = new Polygon([
        [1, 1],
        [1, 2],
        [3, 2],
        [3, 1],
    ]);

    expect($polygon)
        ->getMaxLatitude()->toEqual(3)
        ->getMinLatitude()->toEqual(1)
        ->getMaxLongitude()->toEqual(2)
        ->getMinLongitude()->toEqual(1)
        ->getBoundingBox()->toEqual([[1, 1], [1, 2], [3, 2], [3, 1]]);
});



it('invalid point throws an exception', function () {
    new Polygon([
        [1, 'test'],
        [3, 2],
    ]);
})->throws(InvalidPoint::class);
