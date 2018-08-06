<?php 

namespace ClaimsBundle\Bus\GoodsIssue\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\User;
use ClaimsBundle\Entity\GoodsIssue;
use http\Exception\BadQueryStringException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SetSupplierDecisionCommandHandler extends MessageHandler
{
    public function handle(SetSupplierDecisionCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        $model = $em->getRepository(GoodsIssue::class)->findOneBy(['id' => $command->id,]);
        if (!$model) {
            throw new NotFoundHttpException('Претензия не найдена');
        }

        if (!empty($model->getSupplierDecidedAt())) {
            throw new BadRequestHttpException('Расчет по поставщику уже был завершен до этого');
        }
        if (empty($command->compensation)) {
            throw new BadRequestHttpException('Невозможно подтвердить компенсацию поставщика, если не указана ее сумма');
        }

        $compensation = \ServiceBundle\Components\Number::input($command->compensation);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();

        $model->setSupplierCompensation($compensation);
        $model->setSupplierDecidedAt(new \DateTime());
        $model->setSupplierDecidedBy($currentUser->getId());

        $em->persist($model);
        $em->flush();

        /**
         * @TODO доделать после ввода финансового функционала
         */
//        $debt = \Model::admin('Debt')->add([
//            'comment' => 'Зачет по "' . addslashes($reclamation['name']) . '" из претензии №' . $reclamation['id'],
//            'seller_id' => $reclamation['seller_id'] ? : \Model::admin('Counteragent')->getDefaultSeller()['id'],
//            'supplier_id' => $reclamation['supplier_id']
//        ], $data['compensation'], 5, null, 0);
    }
}