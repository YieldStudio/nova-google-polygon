<?php

namespace YieldStudio\NovaGooglePolygon\Support;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;
use YieldStudio\NovaGooglePolygon\Casts\AsPolygon;
use YieldStudio\NovaGooglePolygon\Exceptions\InvalidPoint;

final class Polygon implements Arrayable, Castable, Jsonable, JsonSerializable
{
    /**
     * @var Point[]
     */
    private array $points = [];

    /**
     * @param array<Point|array> $points
     * @throws InvalidPoint
     */
    public function __construct(array $points)
    {
        $this->setPoints($points);
    }

    /**
     * @param array<Point|array> $points
     * @return $this
     * @throws InvalidPoint
     */
    public function setPoints(array $points): Polygon
    {
        $newPoints = [];

        foreach ($points as $point) {
            if ($point instanceof Point) {
                $newPoints[] = $point;

                continue;
            }

            if (! is_array($point)) {
                throw new InvalidPoint($point);
            }

            $newPoints[] = Point::fromArray($point);
        }

        $this->points = $newPoints;

        return $this;
    }

    public function getMinLatitude(): float
    {
        return min(array_column($this->points, 'lat'));
    }

    public function getMaxLatitude(): float
    {
        return max(array_column($this->points, 'lat'));
    }

    public function getMinLongitude(): float
    {
        return min(array_column($this->points, 'lng'));
    }

    public function getMaxLongitude(): float
    {
        return max(array_column($this->points, 'lng'));
    }

    public function getBoundingBox(): array
    {
        $latitudes = array_column($this->points, 'lat');
        $longitudes = array_column($this->points, 'lng');
        $minLatitude = min($latitudes);
        $minLongitude = min($longitudes);
        $maxLatitude = max($latitudes);
        $maxLongitude = max($longitudes);

        return [
            [$minLatitude, $minLongitude],
            [$minLatitude, $maxLongitude],
            [$maxLatitude, $maxLongitude],
            [$maxLatitude, $minLongitude],
        ];
    }

    /**
     * @throws InvalidPoint
     */
    public function pointOnVertex(Point|array $point): bool
    {
        if (! ($point instanceof Point)) {
            $point = Point::fromArray($point);
        }

        foreach ($this->points as $vertexPoint) {
            if ($point->lat === $vertexPoint->lat && $point->lng === $vertexPoint->lng) {
                return true;
            }
        }

        return false;
    }

    /**
     * @throws InvalidPoint
     */
    public function contain(Point|array $point): bool
    {
        if (! ($point instanceof Point)) {
            $point = Point::fromArray($point);
        }

        $intersections = 0;
        $count = count($this->points);

        if ($this->pointOnVertex($point)) {
            return true;
        }

        for ($i = 1; $i < $count; $i++) {
            $a = $this->points[$i - 1];
            $b = $this->points[$i];

            // Check if point is on a horizontal polygon boundary
            if ($a->lat == $b->lat and $a->lat == $point->lat and $point->lng > min($a->lng, $b->lng) and $point->lng < max($a->lng, $b->lng)) {
                return true;
            }

            if ($point->lat > min($a->lat, $b->lat) and $point->lat <= max($a->lat, $b->lat) and $point->lng <= max($a->lng, $b->lng) and $a->lat != $b->lat) {
                $xinters = ($point->lat - $a->lat) * ($b->lng - $a->lng) / ($b->lat - $a->lat) + $a->lng;

                // Check if point is on the polygon boundary (other than horizontal)
                if ($xinters == $point->lng) {
                    return true;
                }

                if ($a->lng == $b->lng || $point->lng <= $xinters) {
                    $intersections++;
                }
            }
        }

        return $intersections % 2 != 0;
    }

    /**
     * @return Point[]
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function toArray(): array
    {
        return array_map(fn (Point $point) => $point->toArray(), $this->getPoints());
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function toJson($options = 0): bool|string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public static function castUsing(array $arguments)
    {
        return AsPolygon::class;
    }
}
