<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JournalPostType extends AbstractType
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
            ->add('pages', null, ['label' => 'Страницы'])
            ->add('description', null, ['label' => 'Описание статьи', 'attr' => ['class' => 'ckeditor']])
            ->add('body', null, ['label' => 'Текст статьи', 'attr' => ['class' => 'ckeditor']])
            ->add('keywords', null, ['label' => 'Ключевые слова'])
            ->add('author', null, ['label' => 'Авторы'])
            ->add('source', null, ['label' => 'Список литературы', 'attr' => ['class' => 'ckeditor']])
            ->add('file', FileType::class, [ 'label' => 'PDF', 'data_class' => null, 'required' => false])


            ->add('digest', ChoiceType::class, array(
                'choices' => array(
                    'Включить' => true,
                    'Отключить' => false
                ),
                'required'    => true,
                'label' => 'В рассылку'
            ))
            ->add('enabled', ChoiceType::class,  array(
                'choices' => array(
                    'Открыт' => 1,
                    'Закрыт' => 0,
                ),
                'label' => 'Доступ',
                'required'  => false,
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\JournalPost'
        ));
    }
}
