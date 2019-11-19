<?php

namespace App\Form;

use App\Entity\AdminUser;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReviewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users', EntityType::class, [
                'class' => User::class,
                'attr' => ['class' => 'form-control mb-1 selectpicker'],

                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.role = :val')
                        ->setParameter('val', User::ROLE_RECENZENT);
                },
                'choice_label' => function ($user) {
                    return $user->getFirstName() . ' ' . $user->getLastName();
                },
                'multiple' => true,
                'placeholder' => 'Vyber recenzenty',
                'label' => false
            ])
            ->add('id_article', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Přiřadit', 'attr' => ['class' => 'btn btn-secondary', 'style' => 'width:100%']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }


}