<?php

namespace App\Form;

use App\Entity\AdminUser;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-control mb-1', 'mapped' => false,'placeholder'=>'Email'],
                'label'=>false
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control mb-1','placeholder'=>'Heslo'],
                'label'=>false
            ])
            ->add('save', SubmitType::class, ['label' => 'Přihlásit se', 'attr' => ['class' => 'login btn btn-secondary','style'=>'width:100%']]);



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }


}