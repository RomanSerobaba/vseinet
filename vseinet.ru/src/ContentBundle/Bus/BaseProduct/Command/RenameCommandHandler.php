<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use ContentBundle\Entity\BaseProduct;
use ContentBundle\Entity\Category;
use ContentBundle\Entity\Brand;
use ContentBundle\Entity\BaseProductNaming;

class RenameCommandHandler extends MessageHandler
{
    public function handle(RenameCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $where = "";
        if ($command->id) {
            $product = $em->getRepository(BaseProduct::class)->find($command->id);
            if (!$product instanceof BaseProduct) {
                throw new NotFoundHttpException(sprintf('Товар %d не найден', $command->id));
            }
            $category = $em->getRepository(Category::class)->find($product->getCategoryId());
            $where = "AND bp.id = ".$product->getId();
        }
        elseif ($command->categoryId) {
            $category = $em->getRepository(Category::class)->find($command->categoryId);
            if (!$category instanceof Category) {
                throw new NotFoundHttpException(sprintf('Категория %d не найдена', $command->categoryId));    
            }
        }
        elseif ($command->brandId) {
            $brand = $em->getRepository(Brand::class)->find($command->brandId);
            if (!$brand instanceof Brand) {
                throw new NotFoundHttpException(sprintf('Бренд %d не найден', $command->brandId));
            }
            $q = $em->createQuery("
                SELECT DISTINCT c 
                FROM ContentBundle:Category c 
                INNER JOIN ContentBundle:BaseProduct bp WITH bp.categoryId = c.id 
                WHERE bp.brandId = :brandId 
            ");
            $q->setParameter('brandId', $brand->getId());
            $categories = $q->getResult();
            $where = "AND bp.brandId = ".$brand->getId();
        }
        else {
            throw BadRequestHttpException('Не известный критерий отбора товаров для обновления');
        }

        if (empty($categories)) {
            $categories[] = $category;
        }

        foreach ($categories as $category) {
            if ('none' == $category->getTpl() || !$category->getBasename()) {
                continue;
            }

            $naming = $em->getRepository(BaseProductNaming::class)->findBy(['categoryId' => $category->getId()], ['sortOrder' => 'ASC']);
            if (empty($naming)) {
                continue;
            }

            $q = $em->createQuery("
                SELECT 
                    NEW ContentBundle\Bus\BaseProduct\Command\DTO\BaseProduct (
                        bp.id,
                        bp.name,
                        b.name,
                        cc.formedValue,
                        bpdt.model,
                        bpdt.exname
                    )
                FROM ContentBundle:BaseProduct bp
                INNER JOIN ContentBundle:BaseProductData bpdt WITH bpdt.baseProductId = bp.id 
                LEFT OUTER JOIN ContentBundle:Brand b WITH b.id = bp.brandId
                LEFT OUTER JOIN ContentBundle:ColorComposite cc WITH cc.id = bp.colorCompositeId
                WHERE bp.categoryId = :categoryId {$where} 
            ");
            $q->setParameter('categoryId', $category->getId());
            $products = $q->getResult('IndexByHydrator');

            $detailIds = [];
            foreach ($naming as $element) {
                if ($element->getDetailId()) {
                    $detailIds[] = $element->getDetailId();
                }
            }

            if (!empty($detailIds)) {
                $q = $em->createQuery("
                    SELECT 
                        NEW ContentBundle\Bus\BaseProduct\Command\DTO\Detail (
                            d2p.detailId,
                            d.name,
                            d2p.baseProductId,
                            d.typeCode,
                            mu.name,
                            mu.useSpace,
                            d2p.valueId,
                            d2p.value,
                            d.substitutions,
                            dv.value
                        )
                    FROM ContentBundle:DetailToProduct d2p 
                    INNER JOIN ContentBundle:Detail d WITH d.id = d2p.detailId 
                    LEFT OUTER JOIN ContentBundle:MeasureUnit mu WITH mu.id = d.unitId
                    LEFT OUTER JOIN ContentBundle:DetailValue dv WITH dv.id = d2p.valueId
                    WHERE d2p.baseProductId IN (:productIds) AND d2p.detailId IN (:detailIds)
                ");
                $q->setParameter('productIds', array_keys($products));
                $q->setParameter('detailIds', $detailIds);

                $details = [];
                foreach ($q->getArrayResult() as $detail) {
                    $details[$detail->productId][$detail->id] = $detail;
                }
            }

            foreach ($products as $product) {
                $name = '';
                $prevDelimiter = '';
                foreach ($naming as $element) {
                    $value = null;
                    if ($element->getFieldName()) {
                        switch ($element->getFieldName()) {
                            case 'basename':
                                $value = $category->getBasename();
                                break;

                            case 'brand':
                                $value = $product->brand;
                                break;

                            case 'model':
                                $value = $product->model;
                                break;

                            case 'color':
                                $value = $product->color;
                                break;
                        }
                    }
                    else {
                        if (empty($details[$product->id]) || empty($details[$product->id][$element->getDetailId()])) {
                            if ($element->getIsRequired()) {
                                continue 2;
                            }
                            continue;
                        }
                        $value = $details[$product->id][$element->getDetailId()]->getFormedValue();

                    }
                    if (null === $value) {
                        if ($element->getIsRequired()) {
                            continue 2;
                        }
                        continue;
                    }

                    if (!empty($name)) {
                        $name .= $prevDelimiter;
                    }
                    $name .= $element->getDelimiterBefore(true).$value;
                    $prevDelimiter = $element->getDelimiterAfter(true);
                }

                if ($product->name != $name) {
                    $q = $em->createQuery("
                        UPDATE ContentBundle:BaseProduct bp 
                        SET bp.name = :name 
                        WHERE bp.id = :id
                    ");
                    $q->setParameter('name', $name);
                    $q->setParameter('id', $product->id);
                    $q->execute();
                }
            } 
        }
    }
}