<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('avatar', FileType::class, ['label' => 'Фотография', 'data_class' => null, 'required' => false]);
        $builder->add('email', null, ['label' => 'E-mail']);
        $builder->add('username', HiddenType::class);
        $builder->add('lastName', null, ['label' => 'Фамилия']);
        $builder->add('firstName', null, ['label' => 'Имя']);
        $builder->add('surName', null, ['label' => 'Отчество']);
        $builder->add('birthDate', null, ['label' => 'Дата рождения','years' => range(2000,1920)]);
        $builder->add('sex', ChoiceType::class, array(
            'choices' => array(
                'Мужской' => 'M',
                'Женский' => 'W'
            ),
            'required'    => true,
            'label' => 'Пол'
        ));
        $builder->add('country', null, ['label' => 'Страна', 'attr' => ['class' => 'county']]);
        $builder->add('city', null, ['label' => 'Город', 'attr' => ['class' => 'city']]);
        $builder->add('phone', null, ['label' => 'Телефон', 'attr' => ['class' => 'phone']]);

        $builder->add('university', null, [
            'label' => 'Университет',
            'attr' => [
                'data-placeholder' => 'Выберите университет'
            ]
        ]);
        $builder->add('specialty', null, [
            'label' => 'Специальность',
            'data_class' => null,
            'attr' => [
                'class' => 'specialty',
                'data-placeholder' => 'Выберите специальность'
            ]
        ]);

        $builder->add('hobby', null, [
            'label' => 'Интересующие специальности',
            'data_class' => null,
            'attr' => [
                'class' => 'specialty multiselect',
                'data-placeholder' => 'Выберите специальности'
            ]
        ]);

        $builder->add('academicDegree', ChoiceType::class, [
            'label' => 'Ученая степень',
            'choices' => array(
                'Нет' => 0,
                'Кандитат медицинских наук' => 1,
                'Доктор медицинских наук' => 2
            ),
            'required'    => true
        ]);
//        $builder->add('certificate', FileType::class, ['label' => 'Скан сертификата', 'data_class' => null, 'required' => false]);


        $builder->add('workTypeOrganization', ChoiceType::class, [
            'label' => 'Тип организации',
            'choices' => array(
                    'Коммерческая' => 'Коммерческая',
                    'Государтсвенная' => 'Государтсвенная'
            )
        ]);

        $builder->add('workPlace', ChoiceType::class, [
            'label' => 'Вид организации',
            'choices' => array(
                'Поликлинника' => 'Поликлинника',
                'Больница' => 'Больница',
                'НИИ' => 'НИИ',
                'Другое' => 'Другое',
            )
        ]);

        $builder->add('workPlaceTitle', null, ['label' => 'Место работы']);
        $builder->add('workPost', null, ['label' => 'Должность']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

}