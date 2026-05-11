<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ClothingItem;
use App\Entity\Color;
use App\Entity\Pattern;
use App\Entity\Season;
use App\Entity\Style;
use App\Entity\SubCategory;
use App\Entity\WeatherCondition;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends AbstractType<ClothingItem>
 */
class ClothingItemType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['isEdit'] ?? false;

        $builder
            ->add('name', TextType::class, [
                'label' => 'clothing_item.form.label.name',
                'required' => true,
                'constraints' => [new NotBlank()],
                'attr' => ['placeholder' => 'clothing_item.form.placeholder.name', 'class' => 'form-control'],
            ])
            ->add('subCategory', EntityType::class, [
                'label' => 'clothing_item.form.label.subcategory',
                'class' => SubCategory::class,
                'choice_label' => fn (SubCategory $sub) =>
                    $this->translator->trans('category.name.' . $sub->getCategory())
                    . ' - '
                    . $this->translator->trans('subcategory.name.' . $sub->getName()),
                'choice_translation_domain' => false, // don't translate again
                'placeholder' => 'clothing_item.form.placeholder.select',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-select form-select'],
            ])
            ->add('minLayerDepth', IntegerType::class, [
                'label' => 'clothing_item.form.label.min_layer_depth',
                'constraints' => [new Range(
                    notInRangeMessage: 'Min. Layer Depth must be at least 1.',
                    min: 1,
                    max: 5
                )],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('maxLayerDepth', IntegerType::class, [
                'label' => 'clothing_item.form.label.max_layer_depth',
                'constraints' => [new Range(
                    notInRangeMessage: 'Max. Layer Depth must be not more than 5.',
                    min: 1,
                    max: 5
                )],
                'attr' => ['class' => 'form-control'],
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'clothing_item.form.label.notes',
                'required' => false,
                'attr' => [
                    'rows' => 2,
                    'placeholder' => 'clothing_item.form.placeholder.notes',
                    'class' => 'form-control'
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'clothing_item.form.label.photo',
                'required' => !$isEdit,
                'mapped' => false,
                'constraints' => [new File(
                    maxSize: '10M',
                    mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                    maxSizeMessage: 'clothing_item.form.error.image_too_large',
                    mimeTypesMessage: 'clothing_item.form.error.invalid_image',
                )],
                'attr' => ['class' => 'form-control'],
            ])
        ;

        if ($isEdit) {
            // Set the current primary color as the default value
            $currentPrimary = null;
            foreach ($builder->getData()?->getItemColors() ?? [] as $ic) {
                if ($ic->isPrimary()) {
                    $currentPrimary = $ic->getColor();
                    break;
                }
            }

            $builder
                ->add('itemColors', ChoiceType::class, [
                    'label'         => false,
                    'mapped'        => false,
                    'expanded'      => true,
                    'multiple'      => false,
                    'data'          => $currentPrimary,
                    'choices'       => $this->getItemColors($builder->getData()),
                    'choice_label'  => fn (Color $color) => $this->translator->trans('color.name.' . $color->getName()),
                    'choice_value'  => fn (?Color $color) => $color?->getId(),
                    'choice_attr'   => fn (Color $color) => [
                        'data-hex'  => $color->getHexCode(),
                        'data-name' => $color->getName(),
                    ],
                ])
                ->add('itemMaterials', CollectionType::class, [
                    'label'         => false,
                    'entry_type'    => ItemMaterialType::class,
                    'allow_add'     => true,
                    'allow_delete'  => true,
                    'by_reference'  => false,
                    'entry_options' => ['label' => false],
                ])
                ->add('weatherConditions', EntityType::class, [
                    'label' => 'clothing_item.form.label.weather_conditions',
                    'class' => WeatherCondition::class,
                    'choice_label' => fn(WeatherCondition $w) => 'enum.weather_condition.' . $w->getType()->value,
                    'choice_translation_domain' => 'messages',
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'd-flex flex-wrap gap-2'],
                ])
                ->add('patterns', EntityType::class, [
                    'class' => Pattern::class,
                    'choice_label' => fn(Pattern $p) => 'enum.pattern_type.' . $p->getType()->value,
                    'choice_translation_domain' => 'messages',
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'd-flex flex-wrap gap-2'],
                ])
                ->add('seasons', EntityType::class, [
                    'label' => 'clothing_item.form.label.seasons',
                    'class' => Season::class,
                    'choice_label' => fn(Season $s) => 'enum.season.' . $s->getType()->value,
                    'choice_translation_domain' => 'messages',
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'd-flex flex-wrap gap-2'],
                ])
                ->add('styles', EntityType::class, [
                    'label' => 'clothing_item.form.label.styles',
                    'class' => Style::class,
                    'choice_label' => fn(Style $s) => 'enum.style.' . $s->getType()->value,
                    'choice_translation_domain' => 'messages',
                    'multiple' => true,
                    'expanded' => true,
                    'attr' => ['class' => 'd-flex flex-wrap gap-2'],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClothingItem::class,
            'isEdit' => false,
        ]);

        $resolver->setAllowedTypes('isEdit', 'bool');
    }

    /**
     * @param ClothingItem|null $item
     * @return Color[]
     */
    private function getItemColors(?ClothingItem $item): array
    {
        if (!$item) {
            return [];
        }

        $primary   = [];
        $secondary = [];

        foreach ($item->getItemColors() as $ic) {
            if ($ic->getColor() == null) {
                continue;
            }
            if ($ic->isPrimary()) {
                $primary[] = $ic->getColor();
            } else {
                $secondary[] = $ic->getColor();
            }
        }

        return array_merge($primary, $secondary);
    }
}
