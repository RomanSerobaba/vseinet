<?php

namespace AppBundle\ORM\Repository;

trait TreeTrait
{
    /**
     * Create new paths.
     * @param int $id Category id
     * @param int $pid Parent category id
     */
    public function createPaths(int $id, int $pid = null)
    {
        $items = $this->createQueryBuilder('e')
            ->select(['e.id', 'e.pid'])
            ->join($this->getClassPath(), 'ep', 'WITH', 'ep.pid = e.id')
            ->where('ep.id = :pid')
            ->orderBy('ep.plevel')
            ->setParameter('pid', $pid)
            ->getQuery()
            ->getResult();

        $this->walkthru($this->build(array_merge($items, [['id' => $id, 'pid' => $pid]])), [$id]);
        // $this->getEntityManager()->flush();
        // $this->getEntityManager()->clear();
    }

    /**
     * Delete paths.
     * @param int $id Category id
     * @return array Deleted paths [id, pid]
     */
    public function deletePaths(int $id) 
    {
        $items = $this->createQueryBuilder('e')
            ->select(['e.id', 'e.pid'])
            ->join($this->getClassPath(), 'ep', 'WITH', 'ep.id = e.id')
            ->where('ep.pid = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult('ListHydrator');

        $this->getEntityManager()->createQueryBuilder()
            ->delete($this->getClassPath(), 'ep')
            ->where('ep.id IN(:ids)')
            ->setParameter('ids', array_keys($items))
            ->getQuery()
            ->execute();

        return $items;
    }

    /**
     * Update paths.
     * @param int $id Category id
     */
    public function updatePaths(int $id)
    {
        $items = $this->deletePaths($id);
        foreach ($items as $id => $pid) {
            $this->createPaths($id, $pid);
        }
    }

    /**
     * Rebuild all paths.
     */
    public function rebuild()
    {
        $this->getEntityManager()->createQueryBuilder()
            ->delete($this->getClassPath())
            ->getQuery()
            ->execute();

        $items = $this->createQueryBuilder('e')
            ->select(['e.id', 'e.pid'])
            ->getQuery()
            ->getResult();

        $this->walkthru($this->build($items), true);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function getClassPath()
    {
        return $this->getEntityName().'Path';
    }

    private function build(array $items, $pid = null)
    {
        $children = [];
        foreach ($items as $item) {
            if ($item['pid'] === $pid) {
                $children[$item['id']] = $this->build($items, $item['id']);
            }
        }

        return $children;
    }

    private function walkthru(array $tree, $ids, array $pids = [], int $level = 0)
    {
        foreach ($tree as $id => $children) {
            if (!empty($pids)) {
                foreach ($pids as $pid => $plevel) {
                    if (true === $ids) {
                        $this->put($id, $pid, $level, $plevel);
                    }
                    elseif (in_array($id, $ids) || in_array($pid, $ids)) {
                        $this->put($id, $pid, $level, $plevel);
                    }
                }
            }
            if (true === $ids || in_array($id, $ids)) {
                $this->put($id, $id, $level, empty($plevel) ? 0 : $plevel + 1);
            }
            if (!empty($children)) {
                $this->walkthru($children, $ids, $pids + [$id => $level], $level + 1); 
            }
        }
    }

    private function put($id, $pid, $level, $plevel)
    {
        $class = $this->getClassPath();
        $path = new $class();
        $path->setId($id);
        $path->setPid($pid);
        $path->setLevel($level);
        $path->setPlevel($plevel);
        $this->getEntityManager()->persist($path);
    }
}