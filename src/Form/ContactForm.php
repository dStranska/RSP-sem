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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control mb-1', 'mapped' => false,'placeholder'=> 'Email'],
                'label'=>false
            ])
            ->add('subject', TextType::class, [
                'attr' => ['class' => 'form-control mb-1', 'mapped' => false,'placeholder'=>'Předmět zprávy'],
                'label'=>false
            ])
            ->add('message', TextareaType::class, [
                'attr' => ['class' => 'form-control mb-1', 'mapped' => false,'placeholder'=>'Zpráva','cols' => '5', 'rows' => '5'],
                'label'=>false
            ])

            ->add('save', SubmitType::class, ['label' => 'Poslat', 'attr' => ['class' => 'btn btn-secondary','style'=>'width:100%']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }


}