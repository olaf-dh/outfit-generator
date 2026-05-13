<?php

declare(strict_types=1);

namespace App\Form;

use App\Domain\Outfit\Enum\SeasonType;
use App\Domain\Outfit\Enum\StyleType;
use App\Domain\Outfit\Enum\WeatherConditionType;
use App\Entity\ClothingItem;
use App\Repository\ClothingItemRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<ClothingItem>
 */
class OutfitGeneratorType extends AbstractType
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $owner = $this->security->getUser();

        $builder
            ->add('seedItems', EntityType::class, [
                'label'         => 'outfit.generator.form.label.seed_items',
                'class'         => ClothingItem::class,
                'choice_label'  => 'name',
                'multiple'      => true,
                'expanded'      => true,
                'mapped'        => false,
                'query_builder' => function (ClothingItemRepository $repo) use ($owner) {
                    return $repo->createQueryBuilder('c')
                        ->where('c.owner = :owner')
                        ->setParameter('owner', $owner)
                        ->orderBy('c.name', 'ASC');
                },
                'constraints' => [
                    new Count(min: 1, max: 2),
                ],
                'attr' => ['class' => 'form-select', 'size' => 6],
            ])
            ->add('season', EnumType::class, [
                'label' => 'outfit.generator.form.label.season',
                'class' => SeasonType::class,
                'choice_label' => fn (SeasonType $s) => 'enum.season.' . $s->value,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'outfit.generator.form.placeholder.select',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('style', EnumType::class, [
                'label' => 'outfit.generator.form.label.style',
                'class' => StyleType::class,
                'choice_label' => fn(StyleType $s) => 'enum.style.' . $s->value,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'outfit.generator.form.placeholder.select',
                'constraints' => [new NotBlank()],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('weather', EnumType::class, [
                'label' => 'outfit.generator.form.label.weather',
                'class' => WeatherConditionType::class,
                'choice_label' => fn(WeatherConditionType $w) => 'enum.weather_condition.' . $w->value,
                'choice_translation_domain' => 'messages',
                'placeholder' => 'outfit.generator.form.placeholder.weather',
                'required' => false,
                'attr' => ['class' => 'form-select'],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'owner' => null,
        ]);
    }
}
