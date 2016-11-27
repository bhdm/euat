<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecommendationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, ['label' => 'Название статьи'])
            ->add('slug', null, ['label' => 'URL', 'required' => false])
            ->add('authors', null, ['label' => 'Авторы', 'required' => false])
            ->add('description', null, ['label' => 'Описание', 'required' => false])
            ->add('publicBody', null, ['label' => 'Публичный контент', 'attr' => ['class' => 'ckeditor']])
            ->add('body', null, ['label' => 'Контент', 'attr' => ['class' => 'ckeditor']])
            ->add('file', FileType::class, [ 'label' => 'PDF', 'data_class' => null, 'required' => false])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Recommendation'
        ));
    }
}
