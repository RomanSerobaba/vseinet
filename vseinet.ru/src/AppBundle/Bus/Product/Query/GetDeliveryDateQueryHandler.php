<?php 

namespace AppBundle\Bus\Product\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\BaseProduct;
use AppBundle\Entity\GeoPointRoute;
use Cron\CronExpression;

/**
 * @todo
 */
class GetDeliveryDateQueryHandler extends MessageHandler
{
    public function handle(GetDeliveryDateQuery $query)
    {
        $em = $this->getDoctrine()->getManager();

        $baseProduct = $em->getRepository(BaseProduct::class)->find($query->baseProductId);
        if (!$baseProduct instanceof BaseProduct){
            throw new NotFoundHttpException();
        }

        $routes = [];
        foreach ($em->getRepository(GeoPointRoute::class)->findAll() as $route) {
            $routes[$route->getStartingPointId()][$route->getArrivalPointId()] = $route; 
        }
        $path = $this->find($routes, $query->fromPointId, $query->toPointId);
        print_r($path);
        foreach ($path as $route) {
            $cron = CronExpression::factory($route->getSchedule());
            var_dump($cron->getNextRunDate());
        }
// print_r($routes);


        exit;
    }

    protected function find($routes, $from, $to) 
    {
        if (isset($routes[$from][$to])) {
            return [$routes[$from][$to]];
        }
        foreach ($routes[$from] as $id => $route) {
            $next = $this->find($routes, $id, $to);
            if ($next) {
                return array_merge([$routes[$from][$id]], $next);
            }
        }

        return null;
    }
}
