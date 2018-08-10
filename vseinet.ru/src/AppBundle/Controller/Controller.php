<?php 

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormError;

class Controller extends BaseController
{
    protected function addFormErrors(AbstractType $form, array $messages) 
    {
        foreach ($messages as $key => $message) {
            $form->get($key)->addError(new FormError($message));   
        }
    }

    protected function getFormErrors(AbstractType $form)
    {
        $errors = [];
        foreach ($form->all() as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
        }

        return $errors;
    }


    /**
     * @todo: remove it
     */
    protected function checkIsAnonimous()
    {
        if (!$this->get('user.identity')->isAnonimous()) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @todo: remove it
     */
    protected function checkIsAutorized()
    {
        if (!$this->get('user.identity')->isAuthorized()) {
            throw new BadRequestHttpException();
        }
    }

    /**
     * @todo: remove it
     */
    protected function getUserId()
    {
        return $this->get('user.identity')->getUser()->getId();
    }
}
