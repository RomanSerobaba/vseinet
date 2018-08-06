<?php 

namespace ShopBundle\Bus\User\Command;

use AppBundle\Bus\Message\MessageHandler;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Person;
use AppBundle\Entity\User;
use AppBundle\Enum\ContactTypeCode;
use Doctrine\ORM\Query\ResultSetMapping;
use ShopBundle\Entity\BannerMainData;
use ShopBundle\Entity\BannerMainTemplate;
use SupplyBundle\Entity\ViewSupplierOrderItem;
use ReservesBundle\Entity\Shipment;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;

class PasswordCommandHandler extends MessageHandler
{
    public function handle(PasswordCommand $command)
    {
        /**
         * @var \Doctrine\ORM\EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('user.identity')->getUser();
        $id = $currentUser->getId();

        if (!empty($command->password) && $command->password !== $command->passwordConfirm) {
            throw new ValidatorException('Пароли не совпадают');
        }

        $user = $em->getRepository(User::class)->find($id);

        if (!$user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        if (!password_verify($command->passwordCurrent, $user->getPassword())) {
            throw new ValidatorException('Текущий пароль указан не верно');
        }

        $user->setPassword(password_hash($command->password, PASSWORD_BCRYPT, ['cost' => 4]));

        $em->persist($user);
        $em->flush();
    }
}