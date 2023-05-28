<?php

namespace App\Form;

use App\Entity\Imagen;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class ImagenType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'constraints' => [
                    new NotBlank(['message' => 'El título está vacío']),
                ],

            ])
            ->add('descripcion', TextareaType::class, [
                'label' => 'Descripción',
                'required' => false,
            ])
            ->add('image_url', UrlType::class, [
                'label' => 'Enlace de la Imagen',
                'constraints' => [
                    new Url([
                        'message' => 'La URL no es válida.',
                        'normalizer' => 'trim'
                    ]),
                    new NotBlank(['message' => 'El URL está vacío'])
                ],
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Habilitado Para Visualizar',
                'required' => false,
            ])
        ;

        if (!$this->security->isGranted('ROLE_ADMIN')) {
            $builder->remove('status');
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Imagen::class,
        ]);
    }
}
