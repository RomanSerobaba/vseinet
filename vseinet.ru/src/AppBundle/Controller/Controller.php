<?php 

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

class Controller extends BaseController
{
    protected function addFormErrors(FormInterface $form, array $messages) 
    {
        foreach ($messages as $key => $message) {
            $form->get($key)->addError(new FormError($message));   
        }
    }

    protected function getFormErrors(FormInterface $form)
    {
        $errors = [];
        foreach ($form->all() as $child) {
            if ($child->isSubmitted() && !$child->isValid()) {
                foreach ($child->getErrors() as $error) {
                    $errors[$child->getName()][] = $error->getMessage();
                }
            }
            $childErrors = $this->getFormErrorsRecursive($child);    
            if (!empty($childErrors)) {
                $errors[$child->getName()] = $childErrors;
            }
        }

        return $errors;
    }

    public function getGeoCity()
    {
        return $this->get('geo_city.identity')->getGeoCity();
    }

    public function getUserIsEmployee()
    {
        $user = $this->getUser();
        if (null === $user) {
            return false;
        }

        return $user->isEmployee();
    }
}
