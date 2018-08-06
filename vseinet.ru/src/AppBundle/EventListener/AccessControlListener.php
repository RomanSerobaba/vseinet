<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use AppBundle\Security\UserIdentity;
use Doctrine\Common\Annotations\Reader;
use AppBundle\Annotation as VIA;
use Symfony\Component\Yaml\Yaml;

class AccessControlListener
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var UserManager
     */
    protected $identity;

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var string
     */
    protected $rootDir;


    public function __construct(EntityManager $em, UserIdentity $identity, Reader $reader, $rootDir)
    {
        $this->em = $em;
        $this->identity = $identity;
        $this->reader = $reader;
        $this->rootDir = $rootDir;

    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ('OPTIONS' === $request->getMethod()) {
            return;
        }

        if ('dev.vseinet.ru' !== $request->server->get('SERVER_NAME')) {
            return;
        }

        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }
        
        if ($this->identity->isAnonimous()) {
            return;
        }

        $client = $this->identity->getClient();
        if (null === $client) {
            return;
        }

        list($controller, $action) = explode('::', $request->get('_controller'));
        $reflector = new \ReflectionMethod($controller, $action);
        $annotations = $this->reader->getMethodAnnotations($reflector);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof VIA\Route) {
                $method = $annotation->getMethod();
                $path = $annotation->getPath();
                break;
            }
        }   

        $config = Yaml::parse(file_get_contents($this->rootDir.sprintf('%1$sconfig%1$srouting.yml', DIRECTORY_SEPARATOR)));
        foreach ($config as $cfg) {
            $namespace = str_replace(['@', '/'], ['', '\\'], $cfg['resource']);            
            if (false !== strpos($request->get('_controller'), $namespace)) {
                if (isset($cfg['prefix'])) {
                    $prefix = trim($cfg['prefix'], '/');
                    if (!empty($prefix)) {
                        $path = '/'.$prefix.'/'.trim($path, '/').'/';
                    }
                }
                break;
            }
        }

        $q = $this->em->createQuery("
            SELECT am
            FROM AppBundle:ResourceGroup rg 
            INNER JOIN AppBundle:Resource r WITH r.groupId = rg.id 
            INNER JOIN AppBundle:ResourceMethod rm WITH rm.resourceId = r.id 
            INNER JOIN AppBundle:ApiMethod am WITH am.id = rm.apiMethodId
            WHERE rg.clientId = :clientId AND am.method = :method AND am.path = :path
        ");
        $q->setParameter('clientId', $this->identity->getClient()->getId());
        $q->setParameter('method', $method);
        $q->setParameter('path', $path);

        $api = $q->getOneOrNullResult();
        if (null === $api) {
            throw new NotFoundHttpException();
        }

        $q = $this->em->createQuery("
            SELECT amuc.isAllowed  
            FROM AppBundle:ApiMethodUserCodex amuc 
            WHERE amuc.userId = :userId AND amuc.apiMethodId = :apiMethodId 
        ");
        $q->setParameter('userId', $this->identity->getUser()->getId());
        $q->setParameter('apiMethodId', $api->getId());
        $isAllowed = $q->getOneOrNullResult();
        if (true === $isAllowed) {
            return;
        }
        elseif (false === $isAllowed) {
            throw new AccessDeniedHttpException();
        }

        $q = $this->em->createQuery("
            SELECT 1 
            FROM AppBundle:ApiMethodSubroleCodex amsrc 
            INNER JOIN AppBundle:UserToSubrole u2sr WITH u2sr.subroleId = amsrc.subroleId 
            WHERE u2sr.userId = :userId AND amsrc.apiMethodId = :apiMethodId
        ");
        $q->setParameter('userId', $this->identity->getUser()->getId());
        $q->setParameter('apiMethodId', $api->getId());
        $q->setMaxResults(1);
        if (null === $q->getOneOrNullResult()) {
            throw new AccessDeniedHttpException();
        }
    }
}