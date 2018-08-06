<?php 

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DeclensionExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('declension', [$this, 'format']),
        ];
    }

    /**
     * Returns wordform according to numeral and language.
     * 
     * @param integer $numeral
     * @param string $wordforms wordforms separated by a comma
     * @param string $lang
     * 
     * @return string
     */
    public function format($numeral, $wordforms, $lang = 'ru')
    {
        if (empty($wordforms)) {
            return '';
        }
        $wordforms = explode(';', $wordforms);
        if (1 === count($wordforms)) {
            return $wordforms[0];
        }

        $fn = sprintf('format_%s', $lang);
        if (method_exists($this, $fn)) {
            return $this->$fn($numeral, $wordforms);
        }

        return $this->format_en($numeral, $wordforms);
    }

    /**
     * Returns wordform according to rules of english.
     * 
     * @param integer $numeral  
     * @param array $wordforms
     * 
     * @return string
     */
    public function format_en($numeral, array $wordforms)
    {
        return 1 === $numeral ? $wordforms[0] : $wordforms[1];
    }

    /**
     * Returns wordform according to rules of russian.
     * 
     * @param integer $numeral  
     * @param array $wordforms
     * 
     * @return string
     */
    public function format_ru($numeral, array $wordforms)
    {
        if (2 === count($wordforms)) {
            $wordforms[2] = $wordforms[1];
        }
     
        $mod100 = $numeral % 100;

        switch ($numeral % 10) {
            case 1:
                return 11 === $mod100 ? $wordforms[2] : $wordforms[0];

            case 2:
            case 3:
            case 4:
                return 10 < $mod100 && 20 > $mod100 ? $wordforms[2] : $wordforms[1];

            default:
                return $wordforms[2];
         }    
    }
}