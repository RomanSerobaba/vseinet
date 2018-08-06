<?php

namespace SiteBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use AppBundle\Enum\BaseProductImageSize;

class ImageExtension extends AbstractExtension
{
    protected $path;

    public function __construct($path) 
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('image', [$this, 'getImageSrc']),
        ];
    }

    public function getImageSrc($image, $size)
    {
        if ($image->baseSrc) {
            return $this->path.'/'.$image->baseSrc.'_'.BaseProductImageSize::getSize($size).'.jpg';
        }

        return 'nofoto.jpg';
    }
}
