<?php

namespace App\Form;

use App\Entity\Imagen;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
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
            ->add('titulo', null, [
                'label' => 'Título'
            ])
            ->add('descripcion', null, [
                'label' => 'Descripción'
            ])
            ->add('image_url', UrlType::class, [
                'label' => 'Enlace de la Imagen',
                'constraints' => [
                    new Url(['message' => 'La URL no es válida.'])
                ],
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Habilitado Para Visualizar',

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
