<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', null, [ 'label' => 'Название'])
            ->add('category', null, [ 'label' => 'Категория'])
            ->add('adrs', null, [ 'label' => 'Адрес'])
            ->add('start', DateType::class, [ 'label' => 'Дата начала'])
            ->add('end',  DateType::class, [ 'label' => 'Дата окончания'])
            ->add('slug', null, [ 'label' => 'URI'])
            ->add('body', null, [ 'label' => 'Контент', 'attr' => ['class' => 'ckeditor']])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Event'
        ));
    }
}
