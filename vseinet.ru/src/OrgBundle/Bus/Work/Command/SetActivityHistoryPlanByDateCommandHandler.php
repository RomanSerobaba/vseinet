<?php

namespace OrgBundle\Bus\Work\Command;

use AppBundle\Bus\Message\MessageHandler;
use Doctrine\ORM\EntityNotFoundException;
use OrgBundle\Entity\Activity;
use OrgBundle\Entity\ActivityHistory;

class SetActivityHistoryPlanByDateCommandHandler extends MessageHandler
{
    /**
     * @param SetActivityHistoryPlanByDateCommand $command
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function handle(SetActivityHistoryPlanByDateCommand $command)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $command->date = new \DateTime(date('Y-m-01', $command->date ? strtotime($command->date) : time()));

        /** @var Activity $activity */
        $activity = $em->getRepository(Activity::class)
            ->find($command->activityId);

        if (!$activity)
            throw new EntityNotFoundException('Показатель не найден');


        /** @var ActivityHistory $history */
        $history = $em->getRepository(ActivityHistory::class)
            ->findOneBy([
                'activityId' => $command->activityId,
                'date' => $command->date
            ]);

        if (!$history) {
            $history = new ActivityHistory();
            $history->setActivity($activity);
            $history->setDate($command->date);
        }

        $history->setPlanAmount($command->value);

        $em->persist($history);
        $em->flush();
    }
}