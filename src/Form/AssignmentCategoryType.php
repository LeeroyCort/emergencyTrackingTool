<?php

namespace App\Form;

use App\Entity\AssignmentCategory;
use App\Entity\AssignmentGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssignmentCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('categoryGroup', ChoiceType::class, [
                'required' => true,
                'choices'  => [
                    'Dienst' => 0,
                    'Einsatz' => 1,
                ],
            ])
            ->add('containedAssignmentGroups', EntityType::class, [
                'class' => AssignmentGroup::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AssignmentCategory::class,
            'attr' => ['class' => 'generated-form'],
        ]);
    }
}
