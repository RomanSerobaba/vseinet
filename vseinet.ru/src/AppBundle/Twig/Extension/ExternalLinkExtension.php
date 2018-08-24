<?php 

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ExternalLinkExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('external_link', [$this, 'normalize']),
        ];
    }

    public function normalize($link)
    {
        if (0 === strpos($link, 'http')) {
            return $link;
        }

        return 'http://'.$link;
    }
}
