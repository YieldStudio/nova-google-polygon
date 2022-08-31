<?php

namespace YieldStudio\NovaGooglePolygon\Casts;

use Exception;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use YieldStudio\NovaGooglePolygon\Exceptions\InvalidPoint;
use YieldStudio\NovaGooglePolygon\Support\Polygon;

class AsPolygon implements CastsAttributes
{
    /**
     * @throws InvalidPoint
     */
    public function get($model, string $key, $value, array $attributes): ?Polygon
    {
        if (blank($value)) {
            return null;
        }

        $decoded = json_decode($value, true);
        if (blank($decoded)) {
            return null;
        }

        return new Polygon(json_decode($value, true));
    }

    /**
     * @throws Exception
     */
    public function set($model, string $key, $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        if (! ($value instanceof Polygon)) {
            if (! is_array($value)) {
                throw new Exception("$key must be a Polygon instance or an array.");
            }

            $value = new Polygon($value);
        }

        return json_encode($value->getPoints());
    }
}
