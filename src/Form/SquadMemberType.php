<?php

namespace App\Form;

use App\Entity\SquadMember;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SquadMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name1', TextType::class, [
                'required' => true,
            ])
            ->add('name2', TextType::class, [
                'required' => false,
            ])
            ->add('name3', TextType::class, [
                'required' => false,
            ])
            ->add('rank', TextType::class, [
                'required' => false,
            ])
            ->add('scanCode', TextType::class, [
                'required' => true,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SquadMember::class,
            // css klasse um die Formulare Stylen zu koennen
            'attr' => ['class' => 'generated-form'],
        ]);
    }
}
