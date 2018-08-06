<?php 

namespace ContentBundle\Bus\BaseProduct\Command;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ContentBundle\Entity\BaseProductData;
use ContentBundle\Entity\BaseProductEditLog;
use AppBundle\Enum\BaseProductEditTarget;

class SetExnameCommandHandler extends MessageHandler
{
    public function handle(SetExnameCommand $command)
    {
        $em = $this->getDoctrine()->getManager();

        $data = $em->getRepository(BaseProductData::class)->find(['baseProductId' => $command->id]);
        if (!$data instanceof BaseProductData) {
            throw new BadRequestHttpException(sprintf('Товар %d не найден', $command->id));
        }

        if ($data->getExname() != $command->exname) {
            $em->getRepository(BaseProductEditLog::class)->add(
                $product->getId(), 
                BaseProductEditTarget::EXNAME,
                null,
                $this->get('user.identity')->getUser(), 
                $data->getExname(), 
                $command->exname
            );
            $data->setExname($command->exname);
        }
        
        $em->merge($data);
        $em->flush();
    }
}
