<?php 

namespace ContentBundle\Bus\Supplier\SML;

use AppBundle\Container\ContainerAware;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use SupplyBundle\Entity\Supplier;
use SupplyBundle\Entity\SupplierProduct;
use ContentBundle\Entity\Category;

class Transfer extends ContainerAware
{
    protected $supplierCategories = [];

    public function process()
    {
        $em = $this->getDoctrine()->getManager();

        $supplier = $em->getRepository(Supplier::class)->findOneBy(['code' => 'SML']);
        if (!$supplier instanceof Supplier) {
            throw new BadRequestHttpException();
        }

        $supplierProducts = $em->getRepository(SupplierProduct::class)->findBy([
            'baseProductId' => null,
            'supplierId' => $supplier->getId(),
        ], [], 100);

        foreach ($supplierProducts as $supplierProduct) {
            if (empty($this->supplierCategories[$supplierProduct->getCategoryId()])) {
                $q = $em->createQuery("
                    SELECT sc 
                    FROM SupplyBundle:SupplierCategory sc INDEX BY sc.id  
                    INNER JOIN SupplyBundle:SupplierCategoryPath scp WITH scp.pid = sc.id 
                    WHERE scp.id = :id 
                    ORDER BY scp.plevel 
                ");
                $q->setParameter('id', $supplierProduct->getCategoryId());
                $this->supplierCategories += $q->getResult(); 
            }
            $path[] = $supplierCategory = $this->supplierCategories[$supplierProduct->getCategoryId()];
            $pid = $supplierCategory->getPid();
            while (null !== $pid) {
                $supplierCategory = $this->supplierCategories[$pid];
                array_ushift($supplierCategory, $path);
                $pid = $supplierCategory->getPid(); 
            }
            $pid = null;
            foreach ($path as $supplierCategory) {
                $category = $em->getRepository(Category::class)->findOneBy([
                    'pid' => $pid,
                ]);
                if (!$category instanceof Category) {
                    $category = new Category();
                    $category->setPid($pid);
                    $category->setName($supplierCategory->getName());
                    $em->persist($category);
                    $em->flush($category);
                    $em->getRepository(Category::class)->createPaths($category->getId(), $category->getPid());
                }
                $pid = $category->getId();
            }
            $this->get('command_bus')->handle(new TransferCommand([
                'ids' => [$supplierProduct->getId()],
                'categoryId' => $category->getId(),
            ]));
        }
    }
}