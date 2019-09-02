<?php

namespace AppBundle\Container;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use FOS\RestBundle\Context\Context;

class ContainerAware implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Returns true if the service id is defined.
     *
     * @param string $id The service id
     *
     * @return bool true if the service id is defined, false otherwise
     */
    public function has($id)
    {
        return $this->container->has($id);
    }

    /**
     * Gets a container service by its id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    public function get($id)
    {
        return $this->container->get($id);
    }

    /**
     * Gets a container configuration parameter by its name.
     *
     * @param string $name The parameter name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->container->getParameter($name);
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @return Registry
     *
     * @throws \LogicException If DoctrineBundle is not available
     */
    public function getDoctrine()
    {
        if (!$this->container->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }

        return $this->container->get('doctrine');
    }

    public function getUser()
    {
        $token = $this->get('security.token_storage')->getToken();
        if (null === $token) {
            return null;
        }

        $user = $token->getUser();
        if (is_object($user)) {
            return $user;
        }

        return null;
    }

    public function getGeoCity()
    {
        return $this->get('geo_city.identity')->getGeoCity();
    }

    public function getUserIsEmployee()
    {
        $user = $this->getUser();
        if (null === $user) {
            return false;
        }

        return $user->isEmployee();
    }
}
