<?php


namespace App\Form;


use App\Entity\Exception\SkuException;
use App\Entity\Sku;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkuType extends AbstractType implements DataTransformerInterface
{
    /** @inheritDoc */
    public function getParent()
    {
        return TextType::class;
    }

    /** @inheritDoc */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addModelTransformer($this);
    }

    /** @inheritDoc */
    public function transform($value): string
    {
        if ($value === null) {
            return '';
        }

        if (!$value instanceof Sku) {
            $type = gettype($value);
            throw new TransformationFailedException(__METHOD__ . $type . ' received but type Sku expected!');
        }

        return $value->toString();
    }

    /** @inheritDoc */
    public function reverseTransform($value): ?Sku
    {
        if ($value === null) {
            return null;
        }

        try {
            return new Sku($value);
        } catch (SkuException $exception) {
            throw new TransformationFailedException($exception->getMessage());
        }
    }
}