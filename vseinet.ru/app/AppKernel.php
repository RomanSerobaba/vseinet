<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            // new FOS\RestBundle\FOSRestBundle(),
            // new FOS\OAuthServerBundle\FOSOAuthServerBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new SimpleBus\SymfonyBridge\SimpleBusCommandBusBundle(),
            new SimpleBus\SymfonyBridge\SimpleBusEventBusBundle(),
            new Warezgibzzz\QueryBusBundle\WarezgibzzzQueryBusBundle(),
            new SimpleBus\SymfonyBridge\DoctrineOrmBridgeBundle(),
            new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
            new Sensio\Bundle\BuzzBundle\SensioBuzzBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new AppBundle\AppBundle(),
            new ContentBundle\ContentBundle(),
            // new ThirdPartyBundle\ThirdPartyBundle(),
            // new SupplyBundle\SupplyBundle(),
            // new ServiceBundle\ServiceBundle(),
            // new ReservesBundle\ReservesBundle(),
            new OrgBundle\OrgBundle(),
            new PricingBundle\PricingBundle(),
            // new OrderBundle\OrderBundle(),
            // new AccountingBundle\AccountingBundle(),
            // new ClaimsBundle\ClaimsBundle(),
            // new RegisterBundle\RegisterBundle(),
            // new SuppliersBundle\SuppliersBundle(),
            // new MatrixBundle\MatrixBundle(),
            // new DeliveryBundle\DeliveryBundle(),
            new GeoBundle\GeoBundle(),
            // new CatalogBundle\CatalogBundle(),
            // new PromoBundle\PromoBundle(),
            new ShopBundle\ShopBundle(),
            // new DocumentBundle\DocumentBundle(),
            new SiteBundle\SiteBundle(),
            // new FinanseBundle\FinanseBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();

            if ('dev' === $this->getEnvironment()) {
                $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
                $bundles[] = new Symfony\Bundle\WebServerBundle\WebServerBundle();
            }
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
