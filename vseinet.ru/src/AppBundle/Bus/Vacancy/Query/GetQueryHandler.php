<?php 

namespace AppBundle\Bus\Vacancy\Query;

use AppBundle\Bus\Message\MessageHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use AppBundle\Entity\Vacancy;

class GetQueryHandler extends MessageHandler
{
    public function handle(GetQuery $query)
    {
        $vacancy = $this->getDoctrine()->getManager()->getRepository(Vacancy::class)->findOneBy([
            'id' => $query->id,
            'isActive' => true,
        ]);
        if (!$vacancy instanceof Vacancy) {
            throw new NotFoundHttpException('Вакансия не найдена');
        }

        return $vacancy;
    }
}
