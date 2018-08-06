<?php 

namespace SiteBundle\Bus\Catalog;

class Paging
{
    public $current = 1;
    public $count;
    public $perpage;

    public $baseUrl;
    public $attributes;

    public $left = [];
    public $right = [];

    public $next;
    public $prev;

    public $first;
    public $last;

    public function __construct($parameters)
    {
        [
            'total' => $total,
            'page' => $current,
            'perpage' => $perpage,
            'lines' => $lines,
            'baseUrl' => $baseUrl,
            'attributes' => $attributes,
        ] = $parameters;
        $this->count = ceil($total / $perpage);
        if (1 < $this->count) {
            $this->current = $current;
            $this->perpage = $perpage;
            $this->baseUrl = $baseUrl;
            $this->attributes = $attributes;
            for ($page = max(1, $current - $lines); $page < $current; $page++) {
                $this->left[$page] = $this->url($page);
            }
            for ($page = $current + 1; $page <= $current + $lines && $page <= $this->count; $page++) {
                $this->right[$page] = $this->url($page);
            }
            if ($current < $this->count) {
                $this->next = $this->url($current + 1);
            }
            if (1 < $current) {
                $this->prev = $this->url($current - 1);
            }
            if (!empty($this->left)) {
                reset($this->left);
                if (1 < key($this->left)) {
                    $this->first = $this->url(1);
                }
            }
            if (!empty($this->right)) {
                end($this->right);
                if (key($this->right) < $this->count) {
                    $this->last = $this->url($this->count);
                }
            }
        }
    }

    protected function url($page)
    {
        if (1 < $page) {
            $this->attributes['page'] = $page;
        }
        else {
            unset($this->attributes['page']);
        }

        return $this->baseUrl.(empty($this->attributes) ? '' : '?'.http_build_query($this->attributes));
    }
}
