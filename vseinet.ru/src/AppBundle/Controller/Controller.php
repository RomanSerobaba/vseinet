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
            $this->getDeepestChild($form, $key)->addError(new FormError($message));
        }
    }

    private function getDeepestChild(FormInterface $form, string $name)
    {
        if ('' === $name) {
            return $form;
        } elseif (false !== strpos($name, '.')) {
            $chunks = explode('.', $name, 2);

            return $this->getDeepestChild($form->get($chunks[0]), $chunks[1]);
        } else {
            return $form->get($name);
        }
    }

    protected function getFormErrors(FormInterface $form)
    {
        $errors = [];
        $this->getFormErrorsRecursive($form, '', $errors);

        return $errors;
    }

    private function getFormErrorsRecursive(FormInterface $form, $prefix, &$errors)
    {
        if ($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors() as $error) {
                $errors[$prefix.$form->getName()][] = $error->getMessage();
            }
        }

        $children = $form->all();

        if (count($children) > 0) {
            foreach ($children as $child) {
                $this->getFormErrorsRecursive($child, $prefix.$form->getName().'_', $errors);
            }
        }
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

    public function getUserIsWholesaler()
    {
        $user = $this->getUser();
        if (null === $user) {
            return false;
        }

        return $user->isWholesaler();
    }
}
