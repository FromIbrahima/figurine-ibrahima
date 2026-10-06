<?php

namespace App\Form;

use App\Entity\Figurine;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FigurineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Ex. : Chevalier noir peint à la main',
                    'maxlength' => 100,
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 5,
                    'placeholder' => 'Racontez l\'histoire de votre figurine (facultatif)',
                ],
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix (€)',
                'input' => 'string',
                'scale' => 2,
                'html5' => true,
                'attr' => [
                    'min' => 0,
                    'step' => '0.01',
                    'placeholder' => '0,00',
                ],
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image de la figurine',
                'required' => $options['image_required'],
                'allow_delete' => false,
                'download_uri' => false,
                'image_uri' => true,
                'asset_helper' => true,
                'help' => 'JPG, PNG, WEBP ou GIF – 5 Mo maximum.',
                'constraints' => $options['image_required']
                    ? [new NotNull(message: 'Veuillez choisir une image pour votre figurine.')]
                    : [],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figurine::class,
            // L'image est obligatoire à la création, facultative à la modification
            'image_required' => true,
        ]);

        $resolver->setAllowedTypes('image_required', 'bool');
    }
}
