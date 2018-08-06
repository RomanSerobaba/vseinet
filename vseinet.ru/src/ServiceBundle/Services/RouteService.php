<?php

namespace ServiceBundle\Services;

use AppBundle\Bus\Message\MessageHandler;
use ServiceBundle\Components\dijkstra\Graph;
use ServiceBundle\Components\dijkstra\Node;
use ServiceBundle\Components\Dijkstra;

class RouteService extends MessageHandler
{
    /**
     * @param       $fromName
     * @param       $toName
     * @param array $routes
     *
     * @return array
     */
    public function getShortestPath($fromName, $toName, array $routes) : array
    {
        $graph = new Graph();

        foreach ($routes as $route) {
            $from = $route['from'];
            $to = $route['to'];
            $value = $route['value'];

            if (!array_key_exists($from, $graph->getNodes())) {
                $from_node = new Node($from);
                $graph->add($from_node);
            } else {
                $from_node = $graph->getNode($from);
            }

            if (!array_key_exists($to, $graph->getNodes())) {
                $to_node = new Node($to);
                $graph->add($to_node);
            } else {
                $to_node = $graph->getNode($to);
            }

            $from_node->connect($to_node, $value);
        }

        $g = new Dijkstra($graph);
        $start_node = $graph->getNode($fromName);
        $end_node = $graph->getNode($toName);
        $g->setStartingNode($start_node);
        $g->setEndingNode($end_node);

        return [
            'from' => $start_node->getId(),
            'to' => $end_node->getId(),
            'route' => $g->getLiteralShortestPath(),
            'list' => $g->getArrayShortestPath(),
            'total' => $g->getDistance(),
        ];
    }
}