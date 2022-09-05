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

        if ($this->pointOnVertex($point)) {
            return true;
        }

        $intersections = 0;
        $pointA = $this->points[0];
        $n = count($this->points);
        for ($i = 1; $i <= $n; $i++) {
            $pointB = $this->points[$i % $n];

            if (
                $point->lng >= min($pointA->lng, $pointB->lng)
                && $point->lng <= max($pointA->lng, $pointB->lng)
                && $point->lat <= max($pointA->lat, $pointB->lat)
                && $pointA->lng != $pointB->lng) {
                $xinters = ($point->lng - $pointA->lng) * ($pointB->lat - $pointA->lat) / ($pointB->lng - $pointA->lng) + $pointA->lat;
                $pLat = (string)$point->lat;
                $p1Lat = (string)$pointA->lat;
                $p2Lat = (string)$pointB->lat;
                $xintersStr = (string)$xinters;
                if (($pointA->lat == $pointB->lat) ||
                    ($point->lat <= $xinters) ||
                    (bccomp("$p1Lat", "$p2Lat", 14) == 0) ||
                    (bccomp("$pLat", "$xintersStr", 14) == 0) || // pLat == $xinters
                    (bccomp("$pLat", "$xintersStr", 14) == -1)   // pLat < $xinters
                ) {
                    $intersections++;
                }
            }

            $pointA = $pointB;
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
