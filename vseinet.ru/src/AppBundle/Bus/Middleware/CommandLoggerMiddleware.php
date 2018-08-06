<?php 

namespace AppBundle\Bus\Middleware;

use SimpleBus\Message\Bus\Middleware\MessageBusMiddleware;
use AppBundle\Security\UserIdentity;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;


class CommandLoggerMiddleware implements MessageBusMiddleware
{
    /**
     * @var UserManager
     */
    protected $identity;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Dependency Injection
     * 
     * @param UserIdentity $identity 
     * @param string $dir %kernel.logs_dir%
     */
    public function __construct(UserIdentity $identity, $dir)
    {
        $this->identity = $identity;

        $this->logger = new Logger('command-bus');
        $this->logger->pushHandler(new StreamHandler($dir.'/command-bus/command-bus-'.date('Y-m-d').'.log', Logger::INFO));
    }

    /**
     * {@inheritdoc}
     */
    public function handle($command, callable $next)
    {
        $this->logger->info(str_replace(['\\Bus\\', '\\Command\\'], ':', get_class($command)), [
            'user' => $this->getUserId(),
            'command' => json_decode(json_encode($command), true),
        ]);

        $next($command);
    }

    protected function getUserId()
    {
        return $this->identity->isAnonimous() ? null : $this->identity->getUser()->getId();
    }
} 