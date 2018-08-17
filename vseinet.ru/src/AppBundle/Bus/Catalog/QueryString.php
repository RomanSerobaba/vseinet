<?php 

namespace AppBundle\Bus\Catalog;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use AppBundle\Enum\DetailType;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Enum\Sort;
use AppBundle\Bus\Catalog\Enum\SortDirection;
use AppBundle\Bus\Catalog\Query\DTO\Filter\Data;
use AppBundle\Bus\Catalog\Query\DTO\Filter\Range;

class QueryString
{
    const RANGE_DELIMITER = '~';
    const SET_DELIMETER = '|';
    const SORT_DELIMITER = '-';

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;


    /**
     * @param EntityManager $em 
     */
    public function __construct(EntityManager $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param array $query
     * 
     * @return Data
     */
    public function parse(array $query): Data 
    {
        $data = new Data();

        foreach ($query as $key => $value) {
            switch ($key) {
                case 'p': 
                    $data->price = $this->parseRange($value);
                    break;

                case 'b': 
                    $data->brandIds = $this->parseSet($value);
                    break;

                case 'c': 
                    $data->categoryIds = $this->parseSet($value);
                    break;

                case 's': 
                    $data->sectionIds = $this->parseSet($value);
                    break;
                    
                case 'q': 
                    $data->q = $this->parseStr($value);
                    break;

                case 'n':
                    $data->name = $this->parseStr($value);
                    break;

                case 'd': 
                    if (is_array($value)) {
                        $types = $this->getDetailTypes(array_keys($value));
                        foreach ($value as $id => $values) {
                            if (isset($types[$id])) {
                                if (DetailType::CODE_NUMBER === $types[$id]) {
                                    if (null !== ($range = $this->parseRange($values))) {
                                        $data->details[$id] = $range;
                                    }
                                } elseif (DetailType::CODE_ENUM === $types[$id]) {
                                    if (null !== ($set = $this->parseSet($values))) {
                                        $data->details[$id] = $set;
                                    }
                                } elseif (DetailType::CODE_BOOLEAN === $types[$id]) {
                                    if (null !== ($bool = self::parseBool($values))) {
                                        $data->details[$id] = $bool;
                                    }
                                } 
                            }
                        }
                    }
                    break;

                case 'a': 
                    $data->availability = intval($this->parseEnum($value, Availability::class, Availability::ACTIVE));
                    break;

                case 'f': 
                    $data->nofilled = $this->parseSet($value, Nofilled::class);
                    break;

                case 'page':
                    $data->page = $this->parseInt($value);
                    break;

                case 'how':
                    if (false === strpos($value, self::SORT_DELIMITER)) {
                        $sort = $value;
                        $sortDirection = SortDirection::ASC;
                    } else {
                        list($sort, $sortDirection) = explode(self::SORT_DELIMITER, $value);
                    }
                    $data->sort = $this->parseEnum($sort, Sort::class, Sort::DEFAULT);
                    $data->sortDirection = $this->parseEnum($sortDirection, SortDirection::class, SortDirection::ASC);
                    break;
            }
        }

        $data->page = max(1, intval($data->page));

        if (!$this->getUserIsEmployee()) {
            $data->nofilled = null;
            if (Availability::FOR_ALL_TIME === $data->availability) {
                $data->availability = Availability::ACTIVE;
            }
            if (Sort::MARGING === $data->sort) {
                $data->sort = Sort::DEFAULT;
                $data->sortDirection = SortDirection::ASC;
            }
        }

        return $data;
    }

    /**
     * @param Data $data 
     * 
     * @return array           
     */
    public function build(Data $data): array 
    {
        $query = [];
        if ($data->price) {
            $query['p'] = $this->buildRange($data->price);
        }
        if ($data->brandIds) {
            $query['b'] = $this->buildSet($data->brandIds);
        }
        if ($data->categoryIds) {
            $query['c'] = $this->buildSet($data->categoryIds);
        }
        if ($data->sectionIds) {
            $query['s'] = $this->buildSet($data->sectionIds);
        }
        if ($data->q) {
            $query['q'] = $data->q;
        }
        if ($data->name) {
            $query['n'] = $data->name;
        }
        if (!empty($data->details)) {
            $types = $this->getDetailTypes(array_keys($data->details));
            foreach ($data->details as $id => $values) {
                if (isset($types[$id])) {
                    if (DetailType::CODE_NUMBER === $types[$id]) {
                        $query['d'][$id] =  $this->buildRange($values);
                    } elseif (DetailType::CODE_ENUM === $types[$id]) {
                        $query['d'][$id] = $this->buildSet($values);
                    } elseif (DetailType::CODE_BOOLEAN === $types[$id]) {
                        $query['d'][$id] = $values;
                    } 
                }
            }
        }
        if (Availability::ACTIVE !== $data->availability) {
            $query['a'] = $data->availability;
        }
        if ($data->nofilled) {
            $query['f'] = $this->buildSet($data->nofilled);
        }
        if (1 < $data->page) {
            $query['page'] = $data->page;
        }
        if (Sort::DEFAULT !== $data->sort) {
            $query['how'] = $data->sort;
            if (SortDirection::ASC !== $data->sortDirection) {
                $query['how'] .= self::SORT_DELIMITER.$data->sortDirection;
            }
        }

        return $query;
    }

    public function fromPost(array $post, array $query): Data
    {
        $removeKeys = ['p', 'b', 'c', 's', 'n', 'd', 'a', 'f'];
        $query = array_diff_key($query, array_flip($removeKeys));

        foreach ($post as $key => $value) {
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

    protected function getDetailTypes(array $ids): array
    {
        $q = $this->em->createQuery("
            SELECT d.id, d.typeCode 
            FROM AppBundle:Detail d 
            WHERE d.id IN (:ids)
        ");
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

    protected function parseRange(string $value): ?Range
    {
        if (1 === substr_count($value, self::RANGE_DELIMITER)) {
            return new Range(...explode(self::RANGE_DELIMITER, $value));
        }

        return null;
    }

    protected function buildRange(Range $range): ?string
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

            $values = array_filter($values, function($value) use ($constants) {
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
        return preg_replace('/\p{C}+/u', '', urldecode($value)) ?: null;
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
                return implode(QueryString::RANGE_DELIMITER, [$min, $max]);
            }   
        }

        return '';
    }

    protected function toSet($value): string 
    {
        if (is_array($value)) {
            $values = array_map(function($v) { return (string) $v; }, array_keys($value));
            if (!empty($values)) {
                return implode(QueryString::SET_DELIMETER, $values);
            }
        }

        return '';
    }

    protected function getFromArray(array $array, string $key): string
    {
        return array_key_exists($key, $array) ? (string) $array[$key] : '';
    }

    protected function getUserIsEmployee()
    {
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user)) {
                return $user->isEmployee();
            }
        }

        return false;
    }
}
