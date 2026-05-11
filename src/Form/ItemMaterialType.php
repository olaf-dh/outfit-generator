<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ItemMaterial;
use App\Entity\Material;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends AbstractType<ItemMaterial>
 */
class ItemMaterialType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('material', EntityType::class, [
                'label'        => false,
                'class'        => Material::class,
                'choice_label' => fn (Material $m) => $this->translator->trans('material.name.' . $m->getName()),
                'placeholder'  => 'clothing_item.form.placeholder.select',
                'constraints'  => [new NotBlank()],
                'attr'         => ['class' => 'form-select material-select'],
            ])
            ->add('percentage', NumberType::class, [
                'label'       => false,
                'scale'       => 1,
                'constraints' => [
                    new NotBlank(),
                    new Range(min: 0.1, max: 100.0),
                ],
                'attr' => [
                    'class'       => 'form-control percentage-input',
                    'min'         => '0.1',
                    'max'         => '100',
                    'step'        => '0.1',
                    'placeholder' => '%',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ItemMaterial::class,
        ]);
    }
}
