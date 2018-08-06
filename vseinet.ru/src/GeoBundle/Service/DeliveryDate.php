<?php 

namespace GeoBundle\Service;

use AppBundle\Container\ContainerAware;
use GeoBundle\Entity\GeoPointRoute;
use Cron\CronExpression;

/**
 * @development
 */
class DeliveryDate extends ContainerAware
{
    protected $routes;

    public function getDate(int $startingPointId, int $arrivalPointId): \DateTime 
    {
        $this->getRoutes();
        $path = $this->find($startingPointId, $arrivalPointId);
        $date = new \DateTime('tomorrow');
        foreach ($path as $route) {
            $cron = CronExpression::factory($route->getSchedule());
            $date = $cron->getNextRunDate($date);
        print_r($date);
        }
        print_r($path); exit;

    }

    protected function getRoutes()
    {
        if (null === $this->routes) {
            $routes = $this->getDoctrine()->getManager()->getRepository(GeoPointRoute::class)->findAll();
            foreach ($routes as $route) {
                $this->routes[$route->getStartingPointId()][$route->getArrivalPointId()] = $route; 
            }
        }

        return $this->routes;
    }

    protected function find(int $startingPointId, int $arrivalPointId): array
    {
        if (isset($this->routes[$startingPointId][$arrivalPointId])) {
            return [$this->routes[$startingPointId][$arrivalPointId]];
        }

        foreach ($this->routes[$startingPointId] as $transitPointId => $route) {
            $path = $this->find($transitPointId, $arrivalPointId);
            if ($path) {
                return array_merge([$this->routes[$startingPointId][$transitPointId]], $path);
            }
        }

        return null;
    }
}
