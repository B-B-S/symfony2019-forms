<?php


namespace App\Form;


use App\Entity\Category;
use App\Entity\Exception\CategoryException;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Exception;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType implements DataMapperInterface
{
    /**
     * @param Category|null $viewData
     * @param \Traversable|FormInterface[] $forms
     */
    public function mapDataToForms($viewData, $forms)
    {
        if ($viewData === null) {
            return;
        }

        if (!$viewData instanceof Category) {
            throw new UnexpectedTypeException($viewData, Category::class);
        }

        $forms = iterator_to_array($forms);

        // initialize form field values
        $forms['name']->setData($viewData->getName());
        if ($viewData->hasParent()) {
            $forms['parent']->setData($viewData->getParent());
        } else {
            $forms['parent']->setData(null);
        }

    }

    /**
     * @param \Traversable|FormInterface[] $forms
     * @param Category|null $viewData
     */
    public function mapFormsToData($forms, &$viewData)
    {
        if ($viewData === null) {
            return;
        }

        if (!$viewData instanceof Category) {
            throw new UnexpectedTypeException($viewData, Category::class);
        }

        $forms = iterator_to_array($forms);

        try {
            $viewData->rename($forms['name']->getData());
            if($forms['parent']->getData() === null) {
                $viewData->removeParent();
            } else {
                $viewData->moveTo($forms['parent']->getData());
            }
        } catch (CategoryException $exception) {
            $forms['name']->addError(new FormError($exception->getMessage()));
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('parent', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'no parent',
                'query_builder' => static function (EntityRepository $er) use ($options) {
                    if (!array_key_exists('data', $options)) {
                        return;
                    }

                    return $er
                        ->createQueryBuilder('category')
                        ->where('category != :parent')
                        ->setParameter('parent', $options['data']);
                }
            ]);

        $builder->setDataMapper($this);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', Category::class);
        $resolver->setDefault('empty_data', static function (FormInterface $form) {
            try {
                return new Category(
                    $form->get('name')->getData(),
                    $form->get('parent')->getData()
                );
            } catch(CategoryException $exception) {
                $form->get('name')->addError(new FormError($exception->getMessage()));
            }
        });
    }

}