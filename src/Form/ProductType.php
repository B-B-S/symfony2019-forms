<?php


namespace App\Form;


use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('slu', SkuType::class)
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name'
            ])
            ->add('price', PriceType::class);

        $builder->setDataMapper($this);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, 'removeSkuOnPreexistingProduct');
    }

    protected function removeSkuOnPreexistingProduct(PreSetDataEvent $dataEvent) {
        $form = $dataEvent->getForm();

        if($form->get('id') !== 0) {
            $form->remove('sku');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Product::class);
        $resolver->setDefault('empty_data', static function(FormInterface $form) {

            $sku = $form->get('sku')->getData();

            if ($sku === null) {
               return null;
            }

            return new Product(
               $form->get('name')->getData(),
               $form->get('category')->getData(),
               $sku,
               $form->get('price')->getData()
           );
        });
    }

    public function mapDataToForms($viewData, $forms)
    {
        // TODO: Implement mapDataToForms() method.
    }

    public function mapFormsToData($forms, &$viewData)
    {
        // TODO: Implement mapFormsToData() method.
    }


}