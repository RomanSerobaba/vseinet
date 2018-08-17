<?php 

namespace AppBundle\Bus\Catalog\Finder;

use AppBundle\Enum\DetailType;
use AppBundle\Bus\Catalog\Enum\Availability;
use AppBundle\Bus\Catalog\Enum\Nofilled;
use AppBundle\Bus\Catalog\Query\DTO\Category;
use AppBundle\Bus\Brand\Query\DTO\Brand;
use AppBundle\Bus\Catalog\Query\DTO\Filter;

class CategoryProductFinder extends ProductFinder
{
    /**
     * @var Category
     */
    protected $category;

    /**
     * @var Brand
     */
    protected $brand;

    /**
     * @var Filter
     */
    protected $filter;

    
    public function setCategory(Category $category): self  
    {
        $this->category = $category;

        return $this;
    }

    public function setBrand(?Brand $brand): self 
    {
        $this->brand = $brand;

        return $this;
    }
    
    public function getFilter(): Filter 
    {
        if ($this->filter instanceof Filter) {
            return $this->filter;
        }

        $em = $this->getDoctrine()->getManager();

        $detailSelect = "";
        $detailFacets = "";
        if ($this->category->isTplEnabled) {
            $q = $em->createQuery("
                SELECT 
                    NEW AppBundle\Bus\Catalog\Query\DTO\Filter\Detail (
                        d.id,
                        d.name,
                        COALESCE(d.sectionId, 0),
                        d.typeCode,
                        mu.name
                    )
                FROM AppBundle:Detail d
                INNER JOIN AppBundle:DetailGroup dg WITH dg.id = d.groupId 
                LEFT OUTER JOIN AppBundle:MeasureUnit mu WITH mu.id = d.unitId 
                WHERE dg.categoryId = :categoryId AND d.typeCode IN (:typeCodes) AND d.pid IS NULL
                ORDER BY dg.sortOrder, d.sortOrder  
            ");
            $q->setParameter('categoryId', $this->category->id);
            $q->setParameter('typeCodes', [
                DetailType::CODE_NUMBER,
                DetailType::CODE_ENUM,
                DetailType::CODE_BOOLEAN,
            ]);
            $details = $q->getResult('IndexByHydrator');
            foreach ($details as $id => $detail) {
                if (DetailType::CODE_NUMBER === $detail->typeCode) {
                    $detailSelect .= ", MIN(DOUBLE(details.{$id})) AS min_{$id}, MAX(DOUBLE(details.{$id})) AS max_{$id}";
                    continue;
                }
                $detailFacets .= " FACET details.{$id}";
            }
        }

        $query = "
            SELECT {$this->getSelectPrice()}{$detailSelect}
            FROM base_product
            WHERE {$this->getMainCriteria('brands')} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()}
            FACET section_id 
            {$this->getFacetsNofilled()}
            {$detailFacets}
            ;
            SELECT COUNT(*) AS total 
            FROM base_product
            WHERE category_id = {$this->category->id} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()} 
            FACET brand_id
            ;
            SELECT COUNT(*) AS total 
            FROM base_product
            WHERE {$this->getMainCriteria('brands')} AND {$this->getCriteriaAlive()}
            {$this->getFacetAvailability()}
            ;
            SELECT COUNT(*) AS total 
            FROM base_product 
            WHERE {$this->getMainCriteria('brands')} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()}
            ;
        ";
        $results = $this->get('sphinxql')->execute($query);

        $this->filter = new Filter();

        foreach (array_shift($results) as $row) {
            $this->filter->price = new Filter\Range($row['min_price'], $row['max_price']);
            if ($this->category->isTplEnabled) {
                unset($row['min_price'], $row['max_price']);
                $ranges = [];
                foreach ($row as $key => $value) {
                    list($bound, $id) = explode('_', $key, 2);
                    $ranges[$id][$bound] = $value;
                }
                foreach ($ranges as $id => $range) {
                    if (isset($details[$id])) {
                       $details[$id]->values = new Filter\Range($range['min'], $range['max']);
                    }
                }
            }
        }

        if ($this->category->isTplEnabled) {
            $sectionId2count = [];
            foreach (array_shift($results) as $row) {
                $sectionId2count[$row['section_id']] = $row['count(*)'];
            }
            $this->filter->sections = Block\CategorySections::build($sectionId2count, $em);
        }

        foreach (Nofilled::getOptions() as $type => $_) {
            $row = array_shift($results);
            $this->filter->nofilled[$type] = array_key_exists(1, $row) ? $row[1]['count(*)'] : 0;
        }

        foreach ($results as $index => $result) {
            if (array_key_exists('total', $result[0])) {
                break;
            }
            foreach ($result as $row) {
                $keys = array_keys($row);
                $values = array_values($row);
                if (null !== $values[0]) {
                    $id = str_replace('details.', '', $keys[0]);
                    if (isset($details[$id])) {
                        $details[$id]->values[$values[0]] = $values[1];
                    }
                }
            }
        }      
        $valueIds = [];
        foreach ($details as $id => $detail) {
            if (empty($detail->values)) {
                continue;
            }
            if (DetailType::CODE_ENUM === $detail->typeCode) {
                if (1 === count($detail->values)) {
                    continue;
                }
                $valueIds = array_merge($valueIds, array_keys($detail->values)); 
            } elseif (DetailType::CODE_NUMBER === $detail->typeCode && $detail->values->min === $detail->values->max) {
                continue;
            }
            $this->filter->details[$id] = $detail;
        }
        if (!empty($valueIds)) {
            $q = $em->createQuery("
                SELECT
                    NEW AppBundle\Bus\Catalog\Query\DTO\Filter\DetailValue (
                        dv.id,
                        dv.value 
                    )
                FROM AppBundle:DetailValue dv 
                WHERE dv.id IN (:ids)
                ORDER BY dv.value 
            ");
            $q->setParameter('ids', $valueIds);
            $this->filter->values = $q->getResult('IndexByHydrator');
        }
        foreach ($this->filter->details as $id => $detail) {
            if (isset($this->filter->sections[$detail->sectionId])) { 
                $this->filter->sections[$detail->sectionId]->detailIds[] = $id;
            }
            if (0 !== $detail->sectionId) {
                $this->filter->sections[0]->detailIds[] = $id;
            } 
        }
        if (2 === count($this->filter->sections)) {
            $this->filter->sections = array_filter($this->filter->sections, function($section) { return 0 == $section->id; });
        }

        $brandId2count = [];
        foreach ($results[$index + 1] as $row) {
            $brandId2count[$row['brand_id']] = $row['count(*)'];
        }
        $this->filter->brands = Block\Brands::build($brandId2count, $em);

        $geoCityId = $this->getGeoCity()->getRealId();
        foreach ($results[$index + 3] as $row) {
            $availability[$row['availability.'.$geoCityId]] = $row['count(*)'];
        }
        foreach (Availability::getOptions($this->getUserIsEmployee()) as $type => $_) {
            if (!isset($availability[$type])) {
                $availability[$type] = 0;
            }
        }
        $this->filter->availability = Block\Availability::build($availability);

        $this->filter->total = $results[$index + 4][0]['total'];

        return $this->filter;
    }

    public function getFacets(): Filter\Facets
    {
        $filter = $this->getFilter();

        $query = "
            SELECT COUNT(*) AS total
            FROM base_product 
            WHERE {$this->getCriteria()}
            ;
            SELECT {$this->getSelectPrice()}
            FROM base_product
            WHERE {$this->getCriteria('price')}
            ;
            SELECT COUNT(*) AS total
            FROM base_product
            WHERE {$this->getCriteria('brands')}
            FACET brand_id LIMIT 1000
            ;
        ";
        if ($this->category->isTplEnabled) {
            $query .= "
                SELECT COUNT(*) AS total 
                FROM base_product
                WHERE {$this->getCriteria('sections')}
                FACET section_id
                ;
            ";
            foreach ($filter->details as $id => $detail) {
                if (DetailType::CODE_NUMBER === $detail->typeCode) {
                    $query .= "
                        SELECT MIN(DOUBLE(details.{$id})) AS min_{$id}, MAX(DOUBLE(details.{$id})) AS max_{$id}
                        FROM base_product 
                        WHERE {$this->getCriteria($id)}
                        ;    
                    ";
                } else {
                    $query .= "
                        SELECT COUNT(*) AS total 
                        FROM base_product
                        WHERE {$this->getCriteria($id)}
                        FACET details.{$id}
                        ;
                    ";
                }
            }
        }
        $results = $this->get('sphinxql')->execute($query);

        $facets = new Filter\Facets();

        $result = array_shift($results);
        $facets->total = $result[0]['total'];

        $result = array_shift($results);
        if (isset($result[0])) {
            $facets->price = new Filter\Range($result[0]['min_price'], $result[0]['max_price']);
        }
        
        array_shift($results);
        foreach (array_shift($results) as $row) {
            if (isset($filter->brands[$row['brand_id']])) {
                $facets->brandIds[$row['brand_id']] = 1;
            } else {
                $facets->brandIds[-1] = 1;
            }
        }

        if ($this->category->isTplEnabled) {
            array_shift($results);
            foreach (array_shift($results) as $row) {
                $facets->sectionIds[$row['section_id']] = 1;
            }
            foreach ($results as $index => $result) {
                if (isset($result[0]['total'])) {
                    break;
                }
                foreach ($result as $row) {
                    $ranges = [];
                    foreach ($row as $key => $value) {
                        list($bound, $id) = explode('_', $key, 2);
                        $ranges[$id][$bound] = $value;
                    }
                    foreach ($ranges as $id => $range) {
                        $facets->details[$id] = new Filter\Range($range['min'], $range['max']);
                    }
                }
            }
            for ($i = $index + 1; $i < count($results); $i += 2) {
                foreach ($results[$i] as $row) {
                    $keys = array_keys($row);
                    $values = array_values($row);
                    if (null !== $values[0]) {
                        $id = str_replace('details.', '', $keys[0]);
                        $facets->details[$id][$values[0]] = 1;    
                    }
                }
            }
        }

        return $facets;
    }

    protected function getCriteria(string $exclude = null): string
    {
        $filter = $this->getFilter();

        $criteria = "{$this->getMainCriteria($exclude)} AND {$this->getCriteriaAlive()} AND {$this->getCriteriaAvailability()}";
        if ('price' != $exclude && ($condition = $this->getCriteriaPrice())) {
            $criteria .= " AND $condition";
        }
        if ('brands' != $exclude && ($condition = $this->getCriteriaBrands(...$filter->brands))) {
            $criteria .= " AND $condition";
        }
        if ('sections' != $exclude && !empty($data->sectionIds)) {
            $criteria .= " AND section_id IN (".implode(',', $this->data->sectionIds).")";
        }
        if (!empty($this->data->details)) {
            foreach ($this->data->details as $id => $values) {
                if ($id == $exclude || !isset($filter->details[$id])) {
                    continue;
                }
                $detail = $filter->details[$id];
                switch ($detail->typeCode) {
                    case DetailType::CODE_NUMBER:
                        if (null === $values->min) {
                            $criteria .= " AND details.{$id} <= {$values->max}";
                        } elseif (null === $values->max) {
                            $criteria .= " AND details.{$id} >= {$values->min}";
                        } else {
                            $criteria .= " AND details.{$id} BETWEEN {$values->min} AND {$values->max}";
                        }
                        $criteria .= " AND details.{$id} IS NOT NULL";
                        break;

                    case DetailType::CODE_ENUM:
                        $criteria .= " AND details.{$id} IN (".implode(',', $values).")";    
                        break;

                    case DetailType::CODE_BOOLEAN:
                        $criteria .= " AND details.{$id} = {$values}";
                        break;

                    default:
                        throw new \LogicException(sprintf('Detail type %s not supported', $detail->typeCode));
                }
            }
        }
        if ($this->data->name) {
            $criteria .= " AND MATCH('".$this->get('sphinxql')->escapeMatch($this->data->name)."')";
        }

        return $criteria;
    }

    protected function getMainCriteria(string $exclude = null): string
    {
        $criteria = "category_id = {$this->category->id}";
        if ($this->brand && 'brands' != $exclude) {
            $criteria .= " AND brand_id = {$this->brand->id}";
        }

        return $criteria;
    }
}
