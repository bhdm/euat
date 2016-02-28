<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('pages', null, ['label' => 'Страницы'])
            ->add('description', null, ['label' => 'Описание статьи', 'attr' => ['class' => 'ckeditor']])
            ->add('body', null, ['label' => 'Текст статьи', 'attr' => ['class' => 'ckeditor']])
            ->add('keywords', null, ['label' => 'Ключевые слова'])
            ->add('author', null, ['label' => 'Авторы'])
            ->add('source', null, ['label' => 'Список литературы', 'attr' => ['class' => 'ckeditor']])

            ->add('pagesEN', null, ['label' => 'Страницы (Англ)'])
            ->add('titleEn', null, ['label' => 'Название статьи (Англ)'])
            ->add('descriptionEn', null, ['label' => 'Описание статьи (Англ)', 'attr' => ['class' => 'ckeditor']])
            ->add('bodyEn', null, ['label' => 'Текст статьи (Англ)', 'attr' => ['class' => 'ckeditor']])
            ->add('keywordsEn', null, ['label' => 'Ключевые слова (Англ)'])
            ->add('authorEn', null, ['label' => 'Авторы (Англ)'])
            ->add('sourceEn', null, ['label' => 'Список литературы (Англ)', 'attr' => ['class' => 'ckeditor']])

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
