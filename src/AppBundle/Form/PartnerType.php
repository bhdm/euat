<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartnerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['label' => 'Название партнера'])
            ->add('link', null, ['label' => 'Ссылка'])
            ->add('body', TextType::class, ['label' => 'Описание', 'attr' => ['class' => 'ckeditor']])
            ->add('shortDescription', TextType::class, ['label' => 'Название партнера'])
            ->add('image', FileType::class, ['label' => 'Логотип', 'data_class' => null, 'required' => false])
            ->add('sort', IntegerType::class, ['label' => 'Порядок'])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Partner'
        ));
    }
}
