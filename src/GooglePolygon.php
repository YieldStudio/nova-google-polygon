<?php

namespace YieldStudio\NovaGooglePolygon;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;

class GooglePolygon extends Field
{
    public $component = 'google-polygon';

    public $showOnIndex = false;

    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $model->{$attribute} = json_decode($request[$requestAttribute], true);
        }
    }
}
