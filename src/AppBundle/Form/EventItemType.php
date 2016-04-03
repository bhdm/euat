<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventItemType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateType::class, ['label' => 'Дата начала'])
            ->add('timeStart', TimeType::class, ['label' => 'Время начала'])
            ->add('end', DateType::class, ['label' => 'Дата начала'])
            ->add('timeEnd', TimeType::class, ['label' => 'Время окончания'])
//            ->add('city', null, ['label' => 'Город'])
//            ->add('adrs', null, ['label' => 'Адрес'])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\EventItem'
        ));
    }
}
