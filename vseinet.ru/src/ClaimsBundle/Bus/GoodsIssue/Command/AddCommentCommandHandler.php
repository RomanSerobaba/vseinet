<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ClaimsBundle\Entity\GoodsIssue;
use ClaimsBundle\Entity\GoodsIssueComment;
use http\Exception\BadQueryStringException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

class AddCommentCommandHandler extends MessageHandler
{
    public function handle(AddCommentCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $text = trim($command->value);
        if (empty($text)) {
            throw new BadQueryStringException('Не указан комментарий');
        }

        $model = $em->getRepository(GoodsIssue::class)->findOneBy(['id' => $command->id,]);
        if (!$model) {
            throw new NotFoundHttpException('Претензия не найдена');
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $comment = new GoodsIssueComment();
        $comment->setGoodsIssueId($model->getId());
        $comment->setText($text);
        $comment->setCreatedAt(new DateTime());
        $comment->setCreatedBy($currentUser->getId());

        $em->persist($comment);
        $em->flush();
    }
}