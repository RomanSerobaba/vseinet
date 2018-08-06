<?php

namespace ServiceBundle\Components\dijkstra;


interface GraphInterface {

    /**
     * Adds a new node to the current graph.
     *
     * @param NodeInterface $node
     * @return Graph
     * @throws \Exception
     */
    public function add(NodeInterface $node);

    /**
     * Returns the node identified with the $id associated to this graph.
     *
     * @param mixed $id
     * @return Node
     * @throws \Exception
     */
    public function getNode($id);

    /**
     * Returns all the nodes that belong to this graph.
     *
     * @return array
     */
    public function getNodes();
}