<?php

namespace Armincms\Robots\Nova;

use Whitecube\NovaFlexibleContent\Value\ResolverInterface;

class RobotsResolver implements ResolverInterface
{
    /**
     * get the field's value
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @param  Whitecube\NovaFlexibleContent\Layouts\Collection $layouts
     * @return Illuminate\Support\Collection
     */
    public function get($resource, $attribute, $layouts)
    { 
        return collect(Robots::robots())->map(function($attributes, $key) use ($layouts) {  
            return optional($layouts->first())->duplicateAndHydrate($key, $attributes); 
        })->filter()->values();

    }

    /**
     * Set the field's value
     *
     * @param  mixed  $model
     * @param  string $attribute
     * @param  Illuminate\Support\Collection $groups
     * @return string
     */
    public function set($model, $attribute, $groups)
    {  
        $model->{$attribute} = $groups->map->getAttributes()->toArray();
    }
}
