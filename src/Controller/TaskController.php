<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskType;
use App\Repository\TaskRepository;
use App\Form\TaskCreateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface ;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\HttpFoundation\Request;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(Request $request, EntityManagerInterface $entityManager, TaskRepository $taskRepository): Response
    {
        // on recupère le status placé en paramètres via l'url 
        $statusFilter = $request->query->get('status');
       $arrTask = [
    [
        'task_id' => '1',
        'task_name' => 'Conception BDD MERISE',
        'task_details' => 'modèle conceptuel, MERISE...',
        'task_dateCreation' => '12/03/2026',
        'task_dateDealine' => '15/03/2026',
        'task_status' => 'Terminée'
    ],
    [
        'task_id' => '2',
        'task_name' => 'Intégration Symfony Auth',
        'task_details' => 'installation + commandes sécurité',
        'task_dateCreation' => '27/03/2026',
        'task_dateDealine' => '31/03/2026',
        'task_status' => 'Terminée'
    ],
    [
        'task_id' => '3',
        'task_name' => 'Maquettes HTML/CSS Bootstrap',
        'task_details' => 'Figma + intégration Bootstrap',
        'task_dateCreation' => '31/03/2026',
        'task_dateDealine' => '20/04/2026',
        'task_status' => 'En cours'
    ],
    [
        'task_id' => '4',
        'task_name' => 'Config Docker Symfony',
        'task_details' => 'mise en place environnement Docker',
        'task_dateCreation' => '01/04/2026',
        'task_dateDealine' => '04/04/2026',
        'task_status' => 'En cours'
    ],
    [
        'task_id' => '5',
        'task_name' => 'Rédiger les tests unitaires',
        'task_details' => 'tests PHPUnit sur les fonctionnalités',
        'task_dateCreation' => '02/04/2026',
        'task_dateDealine' => '05/04/2026',
        'task_status' => 'En cours'
    ],
    [
        'task_id' => '6',
        'task_name' => 'Dashboard stats page',
        'task_details' => 'création page statistiques',
        'task_dateCreation' => '03/04/2026',
        'task_dateDealine' => '06/04/2026',
        'task_status' => 'Urgence'
    ],
    [
        'task_id' => '7',
        'task_name' => 'Déploiement o2switch',
        'task_details' => 'mise en ligne sur hébergement',
        'task_dateCreation' => '15/03/2026',
        'task_dateDealine' => '20/03/2026',
        'task_status' => 'Terminée'
    ],
    [
        'task_id' => '8',
        'task_name' => 'Dictionnaire de données',
        'task_details' => 'définition des champs BDD',
        'task_dateCreation' => '20/03/2026',
        'task_dateDealine' => '25/03/2026',
        'task_status' => 'Terminée'
    ],
    [
        'task_id' => '9',
        'task_name' => 'Sécurité CSRF Symfony',
        'task_details' => 'protection des formulaires',
        'task_dateCreation' => '04/04/2026',
        'task_dateDealine' => '08/04/2026',
        'task_status' => 'Urgence'
    ],
    [
        'task_id' => '10',
        'task_name' => 'Mise en ligne finale',
        'task_details' => 'déploiement final du projet',
        'task_dateCreation' => '05/04/2026',
        'task_dateDealine' => '10/04/2026',
        'task_status' => 'En cours'
    ],
    ];
      $tasks = []; 
    foreach($arrTask AS $arrTaskList){
         $existingTask = $taskRepository->findOneBy(['name' => $arrTaskList['task_id']]);
        if(!$existingTask){
        
         $objTask = new Task();

        $objTask->setName($arrTaskList['task_name']);
        $objTask->setDetails($arrTaskList['task_details']);
        //En php DateTime ne comprend pas le format jj/mm/aaaa par defaut ('15/03/2026') => 2026-03-15
        $objTask->setDateCreation(
        \DateTime::createFromFormat('d/m/Y', $arrTaskList['task_dateCreation']));

        $objTask->setDateDeadline(
        \DateTime::createFromFormat('d/m/Y', $arrTaskList['task_dateDealine']));
        $objTask->setStatus($arrTaskList['task_status']);
        $entityManager->persist($objTask);
        $tasks[] = $objTask;

        }
    }
            
         $entityManager->flush();

        // récupérer toutes les tâches depuis la BDD
        $allTasks = $taskRepository->findAll();    
        // sauvegarde de toutes les tâches
        $allTasks = $tasks; 

        //  STATISTIQUES (TOUJOURS sur toutes les tâches)
        $countTotal = count($allTasks);
        $countEnd = count(array_filter($allTasks, fn($task) => $task->getStatus() === 'Terminée'));
        $countProgress = count(array_filter($allTasks, fn($t) => $t->getStatus() === 'En cours'));
        $countUrgence = count(array_filter($allTasks, fn($t) => $t->getStatus() === 'Urgence'));


        // On affiche la page en fonction du filtre associé au boutton cliqué !!!
          if ($statusFilter) {
        $tasks = array_filter($tasks, fn($task) => $task->getStatus() === $statusFilter);
        }
        // nombre de tache de la liste 
       $countTask = count($tasks);
      
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
            'task'            => $tasks,
            'count'           => $countTask,
            'countTotal'      => $countTotal,
            'countEnd'        => $countEnd,
            'countProgress'   => $countProgress,
            'countUrgence'    => $countUrgence
           
        ]);
    }
     #[Route('/new', name: 'app_task_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
        {
            $task = new Task();
            // On fixe la date de création automatiquement
            $task->setDateCreation(new \DateTime());
            // On appelle le formulaire avec le createForm
            $form = $this->createForm(TaskCreateFormType::class, $task);
            $form->handleRequest($request);
            // si le formulaire et est si les données sont valides 
            if ($form->isSubmitted() && $form->isValid()) {
            // alors on crée la tache 
               $entityManager->persist($task);
            // on execute et met à jour
                $entityManager->flush();
            $this->addFlash('success' ,"La tache a été bien ajouté en base");

                return $this->redirectToRoute('app_task'); 
            }

            return $this->render('task/new.html.twig', [
                'taskForm' => $form->createView(),
            ]);
        }
    
}