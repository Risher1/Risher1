<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\TaskType;
use App\Entity\TaskGroup;
use DateTime;
use Dom\Text;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;




class TaskCreateFormType extends AbstractType
{   /* creation du formulaire 
    * A partir de la commande symfony console make:form TaskCreateForm
    * definition du type du champ des formulaires 
    */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('details', TextareaType::class)
            ->add('dateDeadline', DateType::class, ['widget' => 'single_text'])
            ->add('status', ChoiceType::class,[
                'choices' => ['En cours' => 'En cours', 'Terminée'=>'Terminée', 'Urgence'=> 'Urgence']
            ])
            //  Gérer la table typetask liée à la table task :
            ->add('taskType', EntityType::class, [
                'class' => TaskType::class,
                'choice_label' => 'type', 
                'placeholder' => 'Choisir un type...'
            ])
           ->add('taskGroup', EntityType::class, [
            'class' => TaskGroup::class,
            'choice_label' => 'name', 
            'placeholder' => 'Choisir un groupe...',
            'required' => false, // facultatif si nullable
            ])
        ;
      
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
