<?php 

namespace AppBundle\Bus\Message;

use AppBundle\Container\ContainerAware;
use Doctrine\Common\Inflector\Inflector;

abstract class MessageHandler extends ContainerAware
{
    /**
     * @todo убрать приведение типов
     * @param array $array
     * @param array $prices
     * @param array $floats
     *
     * @return array
     */
    public function camelizeKeys(array $array, $prices = [], $floats = []) : array
    {

        $result = [];
        $findMePrices = $prices ? array_flip($prices) : [];
        $findMeFloats = $floats ? array_flip($floats) : [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->camelizeKeys($value, $prices, $floats);
            }
            if (array_key_exists($key, $findMePrices)) {
                $value = $value / 100;
            }
            if (array_key_exists($key, $findMeFloats)) {
                $value = (float) $value;
            }
            $result[Inflector::camelize($key)] = $value;
        }

        return $result;
    }
}