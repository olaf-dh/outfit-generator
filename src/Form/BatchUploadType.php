<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ClothingItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends AbstractType<ClothingItem>
 */
class BatchUploadType extends AbstractType
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('photos', FileType::class, [
                'label'    => 'batch_upload.form.label.photos',
                'multiple' => true,
                'mapped'   => false,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Count(min: 1, max: 50),
                    new All([
                        new File(
                            maxSize: '10M',
                            mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
                            maxSizeMessage: $this->translator->trans('form.error.image_too_large'),
                            mimeTypesMessage: $this->translator->trans('form.error.invalid_image'),
                        ),
                    ]),
                ],
                'attr' => [
                    'class'    => 'form-control',
                    'accept'   => 'image/jpeg,image/png,image/webp',
                    'multiple' => true,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
