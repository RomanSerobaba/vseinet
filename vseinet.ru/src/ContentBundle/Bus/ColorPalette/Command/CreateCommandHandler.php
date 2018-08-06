<?php

namespace ContentBundle\Bus\ColorPalette\Command;

use AppBundle\Bus\Message\MessageHandler;
use ContentBundle\Entity\ColorPalette;

class CreateCommandHandler extends MessageHandler
{
    public function handle(CreateCommand $command) 
    {
        $palette = new ColorPalette();
        $palette->setName($command->name);
        $palette->setSortOrder($this->getMaxSortOrder() + 1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($palette);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $palette->getId());
    }

    protected function getMaxSortOrder()
    {
        $query = $this->getDoctrine()->getManager()->createQuery("
            SELECT MAX(cp.sortOrder)
            FROM ContentBundle:ColorPalette cp 
        ");

        try {
            return $query->getSingleScalarResult();
        } 
        catch (NoResultException $e) {
            return 0;
        }
    }
}