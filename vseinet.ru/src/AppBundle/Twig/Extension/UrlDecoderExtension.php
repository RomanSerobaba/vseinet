<?php

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UrlDecoderExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('url_decode', [$this, 'urlDecode']),
        );
    }

    public function urlDecode($value)
    {
        return rawurldecode($value);
    }
}
