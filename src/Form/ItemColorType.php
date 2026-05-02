<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ItemColor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @template-extends AbstractType<ItemColor>
 */
class ItemColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('color', HiddenType::class)
            ->add('isPrimary', CheckboxType::class, [
                'required' => false,
                'label' => false,
            ])
        ;
    }
}

