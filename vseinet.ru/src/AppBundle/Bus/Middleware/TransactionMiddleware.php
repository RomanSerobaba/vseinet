<?php

namespace AppBundle\Bus\Middleware;

use League\Tactician\Middleware;
use Doctrine\ORM\EntityManagerInterface;

class TransactionMiddleware implements Middleware
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param object $command
     * @param callable $next
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function execute($command, callable $next)
    {
        $this->em->beginTransaction();
        try {
            $result = $next($command);
            $this->em->flush();
            $this->em->commit();

            return $result;

        } catch (\Exception $e) {
            $this->em->rollback();

            throw $e;
        }
    }
}
