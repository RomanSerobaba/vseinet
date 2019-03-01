<?php

namespace AppBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;

/**
 * An extended value object to store additional global variables to be accessed in Smarty.
 */
class GlobalVariables extends BaseGlobalVariables
{
    /**
     * Returns the application title.
     *
     * @return string
     */
    public function getDefaultPageTitle()
    {
        return $this->container->getParameter('default.page_title');
    }

    /**
     * Returns the applications description meta.
     *
     * @return string
     */
    public function getDefaultPageDescription()
    {
        return $this->container->getParameter('default.page_description');
    }

    /**
     * Return true if current user is employee.
     *
     * @return bool
     */
    public function getUserIsEmployee()
    {
        $user = $this->getUser();
        if (null === $user) {
            return false;
        }

        return $user->isEmployee();
    }

    /**
     * Return true if current user is programmer.
     *
     * @return bool
     */
    public function getUserIsProgrammer()
    {
        $user = $this->getUser();
        if (null === $user) {
            return false;
        }

        return $user->isProgrammer();
    }

    /**
     * Returns geoCity.
     *
     * @return AppBundle\Entity\GeoCity
     */
    public function getGeoCity()
    {
        return $this->container->get('geo_city.identity')->getGeoCity();
    }

    /**
     * Returns Repsentative.
     *
     * @return AppBundle\Entity\Representative
     */
    public function getRepresentative()
    {
        return $this->container->get('representative.identity')->getRepresentative();
    }

    /**
     * Returns container parameter.
     *
     * @return mixin
     */
    public function getParameter($parameter, $default = null)
    {
        return $this->container->hasParameter($parameter) ? $this->container->getParameter($parameter) : $default;
    }
}
