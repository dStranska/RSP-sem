<?php

namespace App\Form;

use App\Entity\AdminUser;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WriteReviewForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('score',SelectT=)


            ->add('comment', TextType::class, [
                'attr' => ['class' => 'form-control mb-1', 'mapped' => false, 'placeholder' => 'Komentář'],
                'label' => false
            ])

            ->add('value', ChoiceType::class, array(
                'choices' => [
                    1 => 1, 2 => 2, 3=>3,4=>4,5=>5
                ],
                'label'=>'Bodové odohnocení (5-nejlepší, 1-nejhorší)',
                'expanded' => true,
                'attr'=>[
                    'class'=>'mr-4 form-check'
                ]
            ))
            ->add('approved', ChoiceType::class, array(
                'choices' => [
                   'Doporučují ke scvhálení'=>1,'Nedoporučují ke scvhálení'=>0
                ],
                'label'=>'Celkové zhodnocení',
                'expanded' => true,
                'attr'=>[
                    'class'=>'mr-4 form-check'
                ]
            ))

            ->add('save', SubmitType::class, ['label' => 'Přiřadit', 'attr' => ['class' => 'btn btn-secondary', 'style' => 'width:100%']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }


}