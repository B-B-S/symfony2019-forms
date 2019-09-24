<?php


namespace App\Form;


use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidationTypeExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (false === $options['browser_validation']) {
            $view->vars['attr']['novalidate'] = 'novalidate';
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('browser_validation', false);
        $resolver->setAllowedTypes('browser_validation', 'boolean');
    }

    public function getExtendedTypes()
    {
        return [FormType::class];
    }
}