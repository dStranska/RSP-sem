<?php

namespace App\Form;

use App\Entity\AdminUser;
use App\Entity\Article;
use App\Entity\ArticleTheme;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control mb-1', 'mapped' => false,'placeholder'=>'Název'],
                'label'=>false
            ])
            ->add('authors_name', TextType::class, [
                'attr' => ['class' => 'form-control mb-1', 'mapped' => false,'placeholder'=>'Autorský tým'],
                'label'=>false
            ])

            ->add('file', FileType::class, [
                'attr' => ['class' => 'mb-1','placeholder'=>'Soubor','accept'=>'.pdf,.doc'],
                'label'=>false,
            ])
            ->add('theme', EntityType::class, [
                'class' => ArticleTheme::class,
                'attr' => ['class' => 'form-control mb-1'],
                'choice_label' => 'name',
                'placeholder' => 'Téma článku',
                'label' => false
            ])
            ->add('save', SubmitType::class, ['label' => 'Uložit', 'attr' => ['class' => 'btn btn-secondary','style'=>'width:100%']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }


}