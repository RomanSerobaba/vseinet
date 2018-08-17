<?php 

namespace AppBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AutocutExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('autocut', [$this, 'autocut']),
        ];
    }

    public function autocut($text, array $parameters = [])
    {
        $limit = $parameters['limit'] ?? 300;
        $endChar = $parameters['end_char'] ?? '…';     

        $text = preg_replace_callback("#(</?[a-z]+(?:>|\s[^>]*>)|[^<]+)#mi", function($matches) use ($limit, $endChar) {
            static $length = null;
            if (null === $length) {
                $length = $limit;
            }
            
            if ('<' === $matches[0][0]) {
                return $matches[0];
            }
            if (0 >= $length) {
                return '';
            }

            $result = limitChars($matches[0], $length, $endChar);
            $length -= mb_strlen($result) + 1;
            
            return $result;
        }, preg_replace("/<[br|hr|img|iframe][^>]+\>/i", '', $text));
        
        while (preg_match("#<([a-z]+)[^>]*>\s*</\\1>#mi", $text)) {
            $text = preg_replace("#<([a-z]+)[^>]*>\s*</\\1>#mi", '', $text);
        }

        return trim($text);
    }

}

function limitChars($text, $limit, $endChar)
{
    if ('' === trim($text) || mb_strlen($text) <= $limit) {
        return $text;
    }
    if (0 >= $limit) {
        return $endChar;
    }

    if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $text, $matches)) {
        return $endChar;
    }

    return rtrim($matches[0]).(mb_strlen($matches[0]) == mb_strlen($text) ? '' : $endChar);            
}
