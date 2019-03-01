<?php

namespace AppBundle\Bus\Message;

use AppBundle\Container\ContainerAware;
use Doctrine\Common\Inflector\Inflector;

abstract class MessageHandler extends ContainerAware
{
    /**
     * Camelize keys.
     *
     * @param array $array
     *
     * @return array
     */
    public function camelizeKeys(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->camelizeKeys($value);
            }
            $result[Inflector::camelize($key)] = $value;
        }

        return $result;
    }
}
