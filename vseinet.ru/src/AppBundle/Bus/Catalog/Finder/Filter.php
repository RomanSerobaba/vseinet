<?php

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Container\ContainerAware;
use AppBundle\Enum\DetailType;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Catalog\Enum\SortDirection;

class Filter extends ContainerAware
{
    public const RANGE_DELIMITER = '~';
    public const SET_DELIMETER = '|';
    public const SORT_DELIMITER = '-';

    /**
     * @var Range
     */
    public $price;

    /**
     * @var int[]
     */
    public $brandIds = [];

    /**
     * @var int[]
     */
    public $categoryIds = [];

    /**
     * @var int[]
     */
    public $categorySectionIds = [];

    /**
     * @var string
     */
    public $q;

    /**
     * @var string
     */
    public $name;

    /**
     * @var Availability
     */
    public $availability = Availability::ACTIVE;

    /**
     * @var Nofilled[]
     */
    public $nofilled = [];

    /**
     * @var int
     */
    public $page = 1;

    /**
     * @var Sort
     */
    public $sort = Sort::DEFAULT;

    /**
     * @var SortDirection
     */
    public $sortDirection = SortDirection::ASC;

    /**
     * @var array
     */
    public $details = [];

    /**
     * @param array $query
     *
     * @return self
     */
    public function parse($values): self
    {
        foreach ($values as $key => $value) {
            switch ($key) {
                case 'p':
                    $this->price = $this->parseRange($value);
                    break;

                case 'b':
                    $this->brandIds = $this->parseSet($value);
                    break;

                case 'c':
                    $this->categoryIds = $this->parseSet($value);
                    break;

                case 's':
                    $this->categorySectionIds = $this->parseSet($value);
                    break;

                case 'q':
                    $this->q = $this->parseStr($value);
                    break;

                case 'n':
                    $this->name = $this->parseStr($value);
                    break;

                case 'd':
                    if (is_array($value)) {
                        $types = $this->getDetailTypes(array_keys($value));
                        foreach ($value as $id => $values) {
                            if (isset($types[$id])) {
                                if (DetailType::CODE_NUMBER === $types[$id]) {
                                    if (null !== ($range = $this->parseRange($values))) {
                                        $this->details[$id] = $range;
                                    }
                                } elseif (DetailType::CODE_ENUM === $types[$id]) {
                                    if (null !== ($set = $this->parseSet($values))) {
                                        $this->details[$id] = $set;
                                    }
                                } elseif (DetailType::CODE_BOOLEAN === $types[$id]) {
                                    if (null !== ($bool = self::parseBool($values))) {
                                        $this->details[$id] = $bool;
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'a':
                    $this->availability = intval($this->parseEnum($value, Availability::class, Availability::ACTIVE));
                    break;

                case 'f':
                    $this->nofilled = $this->parseSet($value, Nofilled::class);
                    break;

                case 'page':
                    $this->page = $this->parseInt($value);
                    break;

                case 'how':
                    if (false === strpos($value, self::SORT_DELIMITER)) {
                        $sort = $value;
                        $sortDirection = SortDirection::ASC;
                    } else {
                        list($sort, $sortDirection) = explode(self::SORT_DELIMITER, $value);
                    }
                    $this->sort = $this->parseEnum($sort, Sort::class, Sort::DEFAULT);
                    $this->sortDirection = $this->parseEnum($sortDirection, SortDirection::class, SortDirection::ASC);
                    break;
            }
        }

        $this->page = min(QueryBuilder::MAX_MATCHES / QueryBuilder::PER_PAGE, max(1, intval($this->page)));

        if (!$this->getUserIsEmployee()) {
            $this->nofilled = null;
            if (Availability::FOR_ALL_TIME === $this->availability) {
                $this->availability = Availability::ACTIVE;
            }
            if (Sort::MARGING === $this->sort) {
                $this->sort = Sort::DEFAULT;
                $this->sortDirection = SortDirection::ASC;
            }
        }

        return $this;
    }

    /**
     * @param array $request
     *
     * @return self
     */
    public function handleRequest(array $request): self
    {
        $this->reset();
        $query = [];
        foreach ($request as $key => $value) {
            switch ($key) {
                case 'price':
                    $query['p'] = $this->toRange($value);
                    break;

                case 'brand':
                    $query['b'] = $this->toSet($value);
                    break;

                case 'category':
                    $query['c'] = $this->toSet($value);
                    break;

                case 'section':
                    $query['s'] = $this->toSet($value);
                    break;

                case 'name':
                    $query['n'] = (string) $value;
                    break;

                case 'detail':
                    if (is_array($value)) {
                        foreach ($value as $id => $values) {
                            if (is_array($values)) {
                                if (array_key_exists('min', $values) || array_key_exists('max', $values)) {
                                    $query['d'][$id] = $this->toRange($values);
                                    continue;
                                }
                                $query['d'][$id] = $this->toSet($values);
                                continue;
                            }
                            $query['d'][$id] = (string) $values;
                        }
                    }
                    break;

                case 'availability':
                    $query['a'] = intval($value);
                    break;

                case 'nofilled':
                    $query['f'] = $this->toSet($value);
                    break;
            }
        }

        return $this->parse($query);
    }

    /**
     * @param array $extra
     *
     * @return array
     */
    public function build(array $extra = []): array
    {
        $query = [];
        if ($this->price) {
            $query['p'] = $this->buildRange($this->price);
        }
        if ($this->brandIds) {
            $query['b'] = $this->buildSet($this->brandIds);
        }
        if ($this->categoryIds) {
            $query['c'] = $this->buildSet($this->categoryIds);
        }
        if ($this->categorySectionIds) {
            $query['s'] = $this->buildSet($this->categorySectionIds);
        }
        if ($this->q) {
            $query['q'] = $this->q;
        }
        if ($this->name) {
            $query['n'] = $this->name;
        }
        if (!empty($this->details)) {
            $types = $this->getDetailTypes(array_keys($this->details));
            foreach ($this->details as $id => $values) {
                if (isset($types[$id])) {
                    if (DetailType::CODE_NUMBER === $types[$id]) {
                        $query['d'][$id] = $this->buildRange($values);
                    } elseif (DetailType::CODE_ENUM === $types[$id]) {
                        $query['d'][$id] = $this->buildSet($values);
                    } elseif (DetailType::CODE_BOOLEAN === $types[$id]) {
                        $query['d'][$id] = $values;
                    }
                }
            }
        }
        if (Availability::ACTIVE !== $this->availability) {
            $query['a'] = $this->availability;
        }
        if ($this->nofilled) {
            $query['f'] = $this->buildSet($this->nofilled);
        }
        if (1 < $this->page) {
            $query['page'] = $this->page;
        }
        if (Sort::DEFAULT !== $this->sort) {
            $query['how'] = $this->sort;
            if (SortDirection::ASC !== $this->sortDirection) {
                $query['how'] .= self::SORT_DELIMITER.$this->sortDirection;
            }
        }

        return array_merge($query, $extra);
    }

    protected function getDetailTypes(array $ids): array
    {
        $q = $this->getDoctrine()->getManager()->createQuery('
            SELECT d.id, d.typeCode
            FROM AppBundle:Detail AS d
            WHERE d.id IN (:ids)
        ');
        $q->setParameter('ids', $ids);
        $types = $q->getResult('ListHydrator');

        return $types;
    }

    protected function parseEnum(string $value, $enum, $default = null)
    {
        if (!class_exists($enum)) {
            throw new \RuntimeException(sprintf('Class %s not found', $enum));
        }

        $reflector = new \ReflectionClass($enum);
        $constants = $reflector->getConstants();

        return in_array($value, $constants) ? $value : $default;
    }

    protected function parseRange(string $value): ?DTO\Range
    {
        if (1 === substr_count($value, self::RANGE_DELIMITER)) {
            return new DTO\Range(...explode(self::RANGE_DELIMITER, $value));
        }

        return null;
    }

    protected function buildRange(DTO\Range $range): ?string
    {
        [$min, $max] = $range->get();
        if (null !== $min || null !== $max) {
            return implode(self::RANGE_DELIMITER, [$min, $max]);
        }

        return null;
    }

    protected function parseSet(string $value, $enum = null): ?array
    {
        $values = array_map('intval', (explode(self::SET_DELIMETER, $value)));
        if (empty($values)) {
            return null;
        }

        if (class_exists($enum)) {
            $reflector = new \ReflectionClass($enum);
            $constants = $reflector->getConstants();

            $values = array_filter($values, function ($value) use ($constants) {
                return in_array($value, $constants);
            });
        }

        if (empty($values)) {
            return null;
        }

        return array_combine($values, $values);
    }

    protected function buildSet(array $set): string
    {
        return implode(self::SET_DELIMETER, $set);
    }

    protected function parseStr(string $value): ?string
    {
        $str = preg_replace('/\p{C}+/u', '', urldecode($value)) ?: null;
        if (empty($str) || 2 > mb_strlen($str)) {
            return null;
        }

        return $str;
    }

    protected function parseBool(string $value): ?int
    {
        if ('' === $value) {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    protected function parseInt(string $value, int $min = null, int $max = null): ?int
    {
        return filter_var($value, FILTER_VALIDATE_INT, ['min_range' => $min, 'max_range' => $max]);
    }

    protected function toRange($value): string
    {
        if (is_array($value)) {
            $min = $this->getFromArray($value, 'min');
            $max = $this->getFromArray($value, 'max');
            if ($min || $max) {
                return implode(self::RANGE_DELIMITER, [$min, $max]);
            }
        }

        return '';
    }

    protected function toSet($value): string
    {
        if (is_array($value)) {
            $values = array_map(function ($v) { return (string) $v; }, array_keys($value));
            if (!empty($values)) {
                return implode(self::SET_DELIMETER, $values);
            }
        }

        return '';
    }

    protected function getFromArray(array $array, string $key): string
    {
        return array_key_exists($key, $array) ? (string) $array[$key] : '';
    }

    protected function reset(): void
    {
        $this->price = null;
        $this->brandIds = [];
        $this->categoryIds = [];
        $this->categorySectionIds = [];
        // $this->q = null;
        $this->name = null;
        $this->availability = Availability::ACTIVE;
        $this->nofilled = [];
        $this->page = 1;
        $this->sort = Sort::DEFAULT;
        $this->sortDirection = SortDirection::ASC;
        $this->details = [];
    }
}
