<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
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
            ->add('type', ChoiceType::class, array(
                'choices' => array(
                    'Конференции и съезды' => 'CONGRESS',
                    'Школы' => 'SCHOOL',
                    'Партнерcкое' => 'PARTNER'
                ),
                'required'    => true,
                'label' => 'Тип мероприятия'
            ))
            ->add('allowCommentary', ChoiceType::class, array(
                'choices' => array(
                    'Запретить' => false,
                    'Разрешить' => true,
                ),
                'required'    => false,
                'label' => 'Комментарии'
            ))
            ->add('specialties', null, [ 'label' => 'Специальности', 'attr' => ['class' => 'multiselect']])
//            ->add('owner', ChoiceType::class, array(
//                'choices' => array(
//                    'EAT' => 'EAT',
//                    'Партнеры' => 'Partner'
//                ),
//                'required'    => true,
//                'label' => 'Состояние'
//            ))
            ->add('city', null, [ 'label' => 'Город'])
            ->add('adrs', null, [ 'label' => 'Адрес'])
            ->add('start', DateType::class, [ 'label' => 'Дата начала'])
            ->add('timeStart', TimeType::class, [ 'label' => 'Время начала', 'required' => false])
            ->add('end',  DateType::class, [ 'label' => 'Дата окончания', 'required' => false])
            ->add('timeEnd', TimeType::class, [ 'label' => 'Время окончания', 'required' => false])

            ->add('partners', null, [ 'label' => 'Партнеры', 'attr' => ['class' => 'multiselect']])
            ->add('sponsors', null, [ 'label' => 'Спонсоры', 'attr' => ['class' => 'multiselect']])
            ->add('contacts', TextareaType::class, [ 'label' => 'Контакная информация'])
            ->add('slug', null, [ 'label' => 'URI'])
            ->add('metaTitle', null, ['label' => 'МЕТА заголовок'])
            ->add('metaDescription', null, ['label' => 'МЕТА описание'])
            ->add('metaKeyword', null, ['label' => 'МЕТА слова'])
            ->add('body', null, [ 'label' => 'Контент', 'attr' => ['class' => 'ckeditor']])
            ->add('registerIframe', null, [ 'label' => 'Iframe регистрации', 'attr' => ['placeholder' => 'Если Iframe нету, оставьте данное поле пустым']])
            ->add('theses', ChoiceType::class, array(
                'choices' => array(
                    'Активна' => true,
                    'Не активна' => false
                ),
                'required'    => false,
                'label' => 'Вставка тезисов'
            ))
            ->add('register', ChoiceType::class, array(
                'choices' => array(
                    'Активна' => true,
                    'Не активна' => false
                ),
                'required'    => false,
                'label' => 'Регистрация'
            ))
            ->add('digest', ChoiceType::class, array(
                'choices' => array(
                    'Включить' => true,
                    'Отключить' => false
                ),
                'required'    => false,
                'label' => 'В рассылку'
            ))
            ->add('enabled', ChoiceType::class, array(
                'choices' => array(
                    'Активная' => true,
                    'Неактивна' => false
                ),
                'required'    => false,
                'label' => 'Состояние'
            ))

             ->add('items', CollectionType::class, array(
                 'entry_type' => EventItemType::class,
                 'allow_add'    => true,
                 'allow_delete' => true,
                 'label'    => 'Дополнительные даты',
                 'required' => false,
                 'label_attr' => ['style' => 'display: none']
             ));


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
