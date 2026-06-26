<?php

declare(strict_types=1);

namespace App\Form;

use App\Color\Enum\ColorFamily;
use App\Color\Enum\ColorSaturation;
use App\Color\Enum\ColorTemperature;
use App\Color\Enum\ColorTone;
use App\Entity\Color;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<Color>
 */
class PropertyColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'property.form.color.label.name',
                'attr' => [
                    'placeholder' => 'property.form.color.placeholder.term',
                    'class' => 'form-control',
                    'autofocus' => 'autofocus',
                ],
            ])
            ->add('hexCode', ColorType::class, [
                'label' => 'property.form.color.label.hex_code',
                'attr' => [
                    'placeholder' => 'property.form.color.placeholder.hex_code',
                    'class' => 'form-control form-control-color',
                ],
            ])
            ->add('family', EnumType::class, [
                'label' => 'property.form.color.label.family',
                'class' => ColorFamily::class,
                'choice_label' => fn (ColorFamily $f) => 'enum.color_family.' . $f->value,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'property.form.color.placeholder.select',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('tone', EnumType::class, [
                'label' => 'property.form.color.label.tone',
                'class' => ColorTone::class,
                'choice_label' => fn (ColorTone $t) => 'enum.color_tone.' . $t->value,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'property.form.color.placeholder.select',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('temperature', EnumType::class, [
                'label' => 'property.form.color.label.temperature',
                'class' => ColorTemperature::class,
                'choice_label' => fn (ColorTemperature $t) => 'enum.color_temperature.' . $t->value,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'property.form.color.placeholder.select',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('saturation', EnumType::class, [
                'label' => 'property.form.color.label.saturation',
                'class' => ColorSaturation::class,
                'choice_label' => fn (ColorSaturation $s) => 'enum.color_saturation.' . $s->value,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'property.form.color.placeholder.select',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'property.form.button.save',
                'attr' => ['class' => 'btn btn-primary mt-3'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Color::class,
        ]);
    }
}
