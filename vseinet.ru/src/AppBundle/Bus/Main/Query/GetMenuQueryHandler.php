<?php

namespace AppBundle\Bus\Main\Query;

use AppBundle\Bus\Message\MessageHandler;

class GetMenuQueryHandler extends MessageHandler
{
    public function handle(GetMenuQuery $query)
    {
        $cache = $this->get('cache.provider.memcached');
        $cachedMenu = $cache->getItem('menu');
        if ($cachedMenu->isHit()) {
            return $cachedMenu->get();
        }

        $q = $this->getDoctrine()->getManager()->createQuery("
            SELECT
                NEW AppBundle\Bus\Main\Query\DTO\Category (
                    c.id,
                    c.pid,
                    c.name,
                    cp.level
                ),
                CASE
                    WHEN c.id = 5086104 THEN 3
                    WHEN c.id = 33536 THEN 2
                    ELSE 1
                END AS HIDDEN ORD
            FROM AppBundle:Category AS c
            INNER JOIN AppBundle:CategoryPath AS cp WITH cp.id = c.id AND cp.id = cp.pid
            WHERE cp.level <= 3 AND c.id > 0 AND cp.pid != 7562 AND c.countProducts > 0
            ORDER BY cp.plevel ASC, ORD ASC, c.rating ASC
        ");
        $categories = $q->getArrayResult();
        if (empty($categories)) {
            return [];
        }
        $tree = [];
        foreach ($categories as $category) {
            $tree[$category->level][$category->id] = $category;
            if (0 < $category->level) {
                $tree[$category->level - 1][$category->pid]->children[] = $category->id;
            }
        }
        $menu = [];
        foreach ($tree[1] as $id => $category) {
            $ids2 = $category->children;
            $count = count($ids2);
            if (8 < $count) {
                $colsLast= floor($count / 7);
                $ids2Last = array_slice($ids2, 8 - $colsLast);
                if (2 < $colsLast) {
                    $ids2Last2 = array_slice($ids2Last, ($colsLast - 2) * 6);
                    $ids2Last = array_chunk(array_slice($ids2Last, 0, ($colsLast - 2) * 6), 6);
                    $ids2Last = array_merge($ids2Last, array_chunk($ids2Last2, ceil(count($ids2Last2) / 2)));
                } else {
                    $per2Last = ceil(($count + $colsLast - 8) / $colsLast);
                    $ids2Last = array_chunk($ids2Last, $per2Last);
                }
                foreach ($ids2Last as $index => $ids) {
                    foreach ($ids as $id) {
                        $category->last[$index][] = $tree[2][$id];
                    }
                }
                $ids2 = array_slice($ids2, 0, 8 - $colsLast);
            }
            $category->children = [];
            foreach ($ids2 as $id2) {
                $category->children[] = $tree[2][$id2];
            }
            $full2 = [];
            switch ($count) {
                case 3:
                case 2: $full2[1] = true;
                case 1: $full2[0] = true;
                    break;
                case 5: $full2[4] = true;
                case 6:
                case 4: $full2[3] = true;
                    break;
            }
            foreach ($category->children as $index => & $category2) {
                $ids3 = $category2->children;
                if (!isset($full2[$index]) && count($ids3) > 3) {
                    $ids3 = array_slice($ids3, 0, 3);
                    $category2->isAll = true;
                }
                $category2->children = [];
                foreach ($ids3 as $id3) {
                    $category2->children[] = $tree[3][$id3];
                }
            }
            $category->count = $count;
            $menu[] = $category;
        }

        $cachedMenu->set($menu);
        $cachedMenu->expiresAfter(300 + rand(0, 100));
        $cache->save($cachedMenu);

        return $menu;
    }
}
