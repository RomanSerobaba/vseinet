<?php

namespace SiteBundle\Templating;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables as BaseGlobalVariables;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SiteBundle\Bus\Geo\Query\GetCurrentCityQuery;
use SiteBundle\Bus\Representative\Query\GetCurrentRepresentativeQuery;

/**
 * An extended value object to store additional global variables to be accessed in Smarty.
 */
class GlobalVariables extends BaseGlobalVariables
{
    /**
     * Stores User.
     * 
     * @var SiteBundle\Bus\User\Query\DTO\User
     */
    protected $user;

    /**
     * Stores City.
     * 
     * @var SiteBundle\Bus\Geo\Query\DTO\City
     */
    protected $city;

    /**
     * Stores Representative.
     * 
     * @var SiteBundle\Bus\Representative\Query\DTO\Representative
     */
    protected $representative;


    /**
     * Returns the application title.
     *
     * @return string
     */
    public function getDefaultPageTitle()
    {
        return $this->container->getParameter('default.page.title');
    }

    /**
     * Returns the applications description meta.
     *
     * @return string
     */
    public function getDefaultPageDescription()
    {
        return $this->container->getParameter('default.page.description');
    }

    /**
     * Returns user identity.
     * 
     * @return AppBundle\Security\UserIdentity
     */
    public function getUserIdentity()
    {
        return $this->container->get('user.identity');
    }

    /**
     * Returns User.
     * 
     * return SiteBundle\Bus\User\Query\DTO\User
     */
    public function getUser()
    {
        return $this->getUserIdentity()->getUser();
    }

    /**
     * Returns City.
     * 
     * @return SiteBundle\Bus\Geo\Command\DTO\City
     */
    public function getCity()
    {
        if (null === $this->city) {
            $this->container->get('query_bus')->handle(new GetCurrentCityQuery(), $this->city);
        }

        return $this->city;
    }

    /**
     * Returns Repsentative.
     * 
     * @return SiteBundle\Bus\Representative\Command\DTO\Representative
     */
    public function getRepresentative()
    {
        if (null === $this->representative) {
            $this->container->get('query_bus')->handle(new GetCurrentRepresentativeQuery(), $this->representative);
        }

        return $this->representative;
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

    /**
     * Returns csrf token.
     *
     * @return string
     */
    public function getCsrfToken($regenerate = false)
    {
        if ($regenerate) {
            $this->container->get('security.csrf.token_manager')->refreshToken('authenticate');
        }

        return $this->container->get('security.csrf.token_manager')->getToken('authenticate')->getValue();
    }
}
