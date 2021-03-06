<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrezidiumType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', null, ['label' => 'Фамилия'])
            ->add('firstName', null, ['label' => 'Имя'])
            ->add('surName', null, ['label' => 'Отчество'])
            ->add('slug', null, [ 'label' => 'URI'])
            ->add('metaTitle', null, ['label' => 'МЕТА заголовок'])
            ->add('metaDescription', null, ['label' => 'МЕТА описание'])
            ->add('metaKeyword', null, ['label' => 'МЕТА слова'])
            ->add('post', null, ['label' => 'Должность'])
            ->add('photo', FileType::class,['label' => 'Фотография', 'data_class' => null, 'required' => false])
            ->add('description', TextareaType::class, ['label' => 'Описание'])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Prezidium'
        ));
    }
}
