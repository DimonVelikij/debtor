<?php

namespace AppBundle\Form\Admin\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class DebtorsType extends AbstractType
{
    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        //прокидываем объект "Flat" в шаблон "AppBundle/Resources/views/Admin/Form/custom_fields.html.twig"
        $view->vars['object'] = $form->getData()->getOwner();
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return HiddenType::class;
    }
}