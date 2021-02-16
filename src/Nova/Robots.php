<?php

namespace Armincms\Robots\Nova;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Text, Number};
use Laravel\Nova\Http\Requests\NovaRequest;
use Whitecube\NovaFlexibleContent\Flexible;
use Armincms\Nova\ConfigResource;
use NovaItemsField\Items;

 
class Robots extends ConfigResource
{  
    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [ 
            Flexible::make(__('Robots Group'), '_robots_txt_')
                ->resolver(RobotsResolver::class)
                ->addLayout(__('Group'), 'group', [
                    Text::make(__('User Agent'), 'user_agent')
                        ->required()
                        ->rules('required'),

                    Number::make(__('Crawl Delay'), 'crawl_delay')
                        ->required()
                        ->rules('required')
                        ->min(0),

                    Items::make(_('Allow'), 'allow')
                        ->required()
                        ->resolveUsing(function($value) {
                            return is_string($value) ? json_decode($value, true) : $value;
                        }),

                    Items::make(_('Disallow'), 'disallow')
                        ->required()
                        ->resolveUsing(function($value) {
                            return is_string($value) ? json_decode($value, true) : $value;
                        }),

                    Items::make(_('Sitemap'), 'sitemap')
                        ->required()
                        ->resolveUsing(function($value) {
                            return is_string($value) ? json_decode($value, true) : $value;
                        }),
                ]) 
                ->button(__('New Group'))
                ->collapsed(),
        ];
    } 

    /**
     * Get the available meta datas.
     * 
     * @return \\Illuminate\Support\Collection
     */
    public static function robots()
    {
        return collect(static::option('_robots_txt_'));
    } 

    /**
     * Return the location to redirect the user after update.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return tap(parent::redirectAfterUpdate($request, $resource), function() {
            static::cleanRobotSTxt();

            static::robots()->filter()->each(function($robot) {
                static::appendToRobots('User-agent: '. $robot['user_agent']);
                if(intval($robot['crawl_delay']) > 0)
                    static::appendToRobots('Crawl-delay: '. $robot['crawl_delay']);
                collect($robot['allow'])->filter()->each(function($allow) {
                    static::appendToRobots('Allow: '. $allow);
                });
                collect($robot['disallow'])->filter()->each(function($disallow) {
                    static::appendToRobots('Disallow: '. $disallow);
                });
                collect($robot['sitemap'])->filter()->each(function($sitemap) {
                    static::appendToRobots('Sitemap: '. $sitemap);
                });
                
                static::appendToRobots("\r\n");
            });
        });
    }

    /**
     * Clean the robots.txt file.
     * 
     * @return static
     */
    public static function cleanRobotSTxt()
    { 
        \File::put(public_path('robots.txt'), ''); 

        return static::class;
    }

    /**
     * Append the given text to the robot.txt file.
     * 
     * @param  string $text 
     * @return static       
     */
    public static function appendToRobots($text)
    { 
        \File::append(public_path('robots.txt'), $text."\r\n"); 

        return static::class;
    }
}
