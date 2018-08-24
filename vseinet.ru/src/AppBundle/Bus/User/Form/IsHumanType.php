<?php 

namespace AppBundle\Bus\User\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class IsHumanType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    protected $security;


    public function __construct(TokenStorageInterface $security)
    {
        $this->security = $security;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('mapped', false); 

        $token = $this->security->getToken();
        if (null !== $token) {
            $user = $token->getUser();
            if (is_object($user)) {
                $resolver->setDefault('required', false);
            }
        }
    }

    public function getParent()
    {
        return CheckboxType::class;
    }
}
