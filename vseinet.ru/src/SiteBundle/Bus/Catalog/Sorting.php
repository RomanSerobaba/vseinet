<?php 

namespace SiteBundle\Bus\Catalog;

use SiteBundle\Bus\Catalog\Enum\Sort;
use SiteBundle\Bus\Catalog\Enum\SortDirection;

class Sorting 
{
    public $options;

    public $sort;

    public $sortDirection;

    public $baseUrl;

    public $attributes;

    public function __construct(array $parameters)
    {
        [
            'options' => $options,
            'sort' => $sort,
            'sortDirection' => $sortDirection,
            'baseUrl' => $baseUrl,
            'attributes' => $attributes,
        ] = $parameters;
        $this->sort = $sort;
        $this->sortDirection = $sortDirection;
        $this->baseUrl = $baseUrl;
        $this->attributes = $attributes;

        $this->options = [];
        foreach ($options as $key => $title) {
            $direction = SortDirection::ASC;
            if ($key === $sort && SortDirection::ASC === $sortDirection && Sort::RATING !== $sort) {
                $direction = SortDirection::DESC;
            }    
            $this->options[$key] = [
                'title' => $title,
                'url' => $this->url($key, $direction),
            ];
        }
    }

    protected function url($sort, $direction)
    {
        $this->attributes['how'] = $sort;
        if (SortDirection::ASC !== $direction) {
            $this->attributes['how'] .= '-'.$direction;
        }

        return $this->baseUrl.(empty($this->attributes) ? '' : '?'.http_build_query($this->attributes));
    }
}