<?php 

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use GeoBundle\Service\PhoneFormatter;

class PhoneFormatExtension extends AbstractExtension
{
    /**
     * @var PhoneFormatter
     */
    protected $formatter;


    public function __construct(PhoneFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new TwigFilter('phone_format', [$this, 'format']),
        );
    }

    /**
     * Returns formated phone.
     * 
     * @param string $phone
     *  
     * @return string
     */
    public function format(string $phone): string
    {
        return $this->formatter->format($phone);
    }
}
