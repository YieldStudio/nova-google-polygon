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
        ->contain([0, 0])->toBeFalse()
        ->contain([2, 5])->toBeFalse()
        ->contain([1, 1])->toBeTrue()
        ->contain([2.5, 1.5])->toBeTrue();
});

it('point in polygon with lat/lng', function () {
    $polygon = new Polygon([
        [48.88296174, 2.38560516],
        [48.88310990, 2.386999901],
        [48.88318398, 2.387804571],
        [48.88334273, 2.388308826],
        [48.88350500, 2.388909641],
        [48.88369549, 2.389467540],
        [48.88410468, 2.390583339],
        [48.88435161, 2.390288296],
        [48.88465498, 2.390250745],
        [48.88528993, 2.390132728],
        [48.88609419, 2.389971796],
        [48.88653158, 2.390111270],
        [48.88706774, 2.389767948],
        [48.88798485, 2.389188591],
        [48.88646104, 2.381678405],
        [48.88471142, 2.383609596],
    ]);

    expect($polygon)
        ->contain([48.886821, 2.384657])->toBeTrue()
        ->contain([48.885984, 2.383921])->toBeTrue()
        ->contain([48.886965, 2.38463])->toBeTrue()
        ->contain([48.885819, 2.382452])->toBeTrue()
        ->contain([48.88296174, 2.38560516])->toBeTrue()
        ->contain([48.883459, 2.381774])->toBeFalse()
        ->contain([48.887191, 2.384579])->toBeFalse()
        ->contain([48.885067, 2.383058])->toBeFalse()
        ->contain([48.88296174, 2.38560515])->toBeFalse();
});


it('get bounding box', function () {
    $polygon = new Polygon([
        [1, 1],
        [1.01, 1],
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
