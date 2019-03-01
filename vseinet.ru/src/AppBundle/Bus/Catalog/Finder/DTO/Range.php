<?php

namespace AppBundle\Bus\Catalog\Finder\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class Range implements \Countable
{
    const PRECISION = 4;

    /**
     * @Assert\Type(type="float")
     */
    public $min;

    /**
     * @Assert\Type(type="float")
     */
    public $max;

    /**
     * @Assert\Type(type="float")
     */
    public $step = 1;


    public function __construct(string $min, string $max)
    {
        $this->set($min, $max);
    }

    public function set(string $min, string $max): self
    {
        $this->min = empty($min) ? null : $this->sanitize($min);
        $this->max = empty($max) ? null : $this->sanitize($max);
        if (null === $this->max) {
            $this->min = null;
        } elseif (null === $this->min) {
            $this->min = 0;
        }

        if ($this->max) {
            $exp = $this->exp($this->max);
            if ($this->min) {
                $exp = min($exp, $this->exp($this->min));
            }
            if (0 > $exp) {
                $this->step = pow(10, $exp);
            } elseif (1 > $this->max / 10) {
                $this->step = 0.1;
            }
        }

        return $this;
    }

    public function get(): array
    {
        return [$this->min, $this->max];
    }

    public function count()
    {
        return ($this->max - $this->min) / $this->step;
    }

    protected function sanitize($number): float
    {
        return round(floatval(str_replace([' ', ','], ['', '.'], (string) $number)), self::PRECISION);
    }

    protected function exp(float $value): float
    {
        $e = -self::PRECISION;
        if ($value = round(round($value, self::PRECISION) * pow(10, self::PRECISION))) {
            foreach (str_split(strrev($value)) as $char) {
                if ('0' === $char) {
                    $e++;
                } else {
                    break;
                }
            }
        }

        return $e;
    }
}
