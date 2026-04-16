<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\TaskGroup;
use App\Entity\TaskType;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskShareType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task', EntityType::class, [
                'class' => Task::class,
                'choice_label' => 'name',
                'attr' => [
                    'placeholder' => '--Choisir un type de tâche--',
                ],
            ])
            ->add('taskSharedMessage', null, [
                'label' => 'Message (optionnel)',
                'attr' => [
                    'placeholder' => 'Ajouter une note pour le collaborateur...'
                ]
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'attr' => [
                    'placeholder' => '--Choisir un utilisateur--'
                ]
            ])
         ;  
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
