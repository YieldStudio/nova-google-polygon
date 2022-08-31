<?php

namespace YieldStudio\NovaGooglePolygon\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use YieldStudio\NovaGooglePolygon\Exceptions\InvalidPoint;

final class Point implements Arrayable, Jsonable, JsonSerializable
{
    public function __construct(public readonly float $lat, public readonly float $lng)
    {
    }

    /**
     * @throws InvalidPoint
     */
    public static function fromArray(array $input): Point
    {
        if (array_key_exists('lat', $input) && array_key_exists('lng', $input)) {
            $lat = $input['lat'];
            $lng = $input['lng'];
        } elseif (count($input) >= 2) {
            [$lat, $lng] = $input;
        }

        if (! isset($lat) || ! isset($lng) || ! is_numeric($lat) || ! is_numeric($lng)) {
            throw new InvalidPoint($input);
        }

        return new Point($lat, $lng);
    }

    public function toArray(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
