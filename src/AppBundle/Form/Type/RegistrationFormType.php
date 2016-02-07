<?php
namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', null, ['label' => 'Email']);
        $builder->add('phone', null, ['label' => 'Телефон']);
        $builder->add('lastName', null, ['label' => 'Фамилия']);
        $builder->add('firstName', null, ['label' => 'Имя']);
        $builder->add('surName', null, ['label' => 'Отчество']);
        $builder->add('birthDate', null, ['label' => 'Дата рождения']);
        $builder->add('sex', null, ['label' => 'Пол']);
        $builder->add('plainPassword', null, ['label' => 'Пароль']);
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

}