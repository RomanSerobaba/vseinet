<?php

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use AppBundle\Enum\BaseProductImage;

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

    public function getImageSrc($image, $size, $ext = 'jpg')
    {
        if (null !== $image && $image->baseSrc) {
            return BaseProductImage::buildSrc($this->path, $image->baseSrc, $size, $ext);
        }

        return '/images/nophoto_'.BaseProductImage::getSize($size).'.jpg';
    }
}
