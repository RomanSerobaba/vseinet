<?php 

namespace OrgBundle\Bus\Complaint\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use OrgBundle\Entity\Complaint;
use OrgBundle\Entity\ComplaintComment;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddCommentCommandHandler extends MessageHandler
{
    public function handle(AddCommentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        /**
         * @var Complaint $complaint
         */
        $complaint = $em->getRepository(Complaint::class)->find($command->id);

        if (!$complaint) {
            throw new NotFoundHttpException('Жалоба не найдена');
        }

        $model = new ComplaintComment();
        $model->setText($command->text);
        $model->setComplaintId($complaint->getId());
        $model->setCreatedAt(new \DateTime());
        $model->setCreatedBy($currentUser->getId());

        $em->persist($model);
        $em->flush();

        $this->get('uuid.manager')->saveId($command->uuid, $model->getId());
    }
}