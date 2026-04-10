<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskType;
use App\Entity\TaskGroup;
use App\Repository\TaskRepository;
use App\Form\TaskCreateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    public function index(Request $request, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        $statusFilter = $request->query->get('status');

        // Stats habituelles
        $countTotal    = $taskRepository->count([]);
        $countEnd      = $taskRepository->count(['status' => 'Terminée']);
        $countProgress = $taskRepository->count(['status' => 'En cours']);
        $countUrgence  = $taskRepository->count(['status' => 'Urgence']);

        $tasks = $statusFilter
            ? $taskRepository->findBy(['status' => $statusFilter])
            : $taskRepository->findAll();

        // 1. Définition des noms de groupes que l'on veut tester
        $targetGroupNames = ['Maquettes UI/UX', 'Sécurité', 'Projet Symfony'];

        $groupRepo = $entityManager->getRepository(TaskGroup::class);
        $messages = []; 

        foreach ($targetGroupNames as $name) {
            $group = $groupRepo->findOneBy(['name' => $name]);

            if (!$group) {
                $messages[] = "Le groupe '$name' n'existe pas du tout en base.";
                continue;
            }

            $taskFound = $taskRepository->findOneBy(['taskgroup' => $group]);

            if ($taskFound) {
                $entityManager->refresh($taskFound); 
                $messages[] = $taskFound->getTaskGroup()->getName();
            } else {
                $messages[] = "Le groupe '$name' existe, mais aucune tâche n'y est rattachée.";
            }
        }

        // Affichage du nombre taches en fonction du groupe
        $countGroup1 = $taskRepository->count(['taskgroup' => $groupRepo->findOneBy(['name' => 'Maquettes UI/UX'])]);  
        $countGroup2 = $taskRepository->count(['taskgroup' => $groupRepo->findOneBy(['name' => 'Sécurité'])]);
        $countGroup3 = $taskRepository->count(['taskgroup' => $groupRepo->findOneBy(['name' => 'Projet Symfony'])]);

        /*********** Affichage de la date d'échéance proche ***********/
        
        $today = new \DateTime('today');
        $threeDaysLater = (clone $today)->modify('+3 days');
        
        $messagesDeadlines = "";
        $messagesDeadlines2 = "";

        // Recherche spécifique Task 1
        $specificTask = $taskRepository->findOneBy([
            'name'         => 'Intégration Symfony Auth',
            'dateDeadline' => $threeDaysLater
        ]);

        // Recherche spécifique Task 2
        $specificTask2 = $taskRepository->findOneBy([
            'name'         => 'Déploiement o2switch',
            'dateDeadline' => $today
        ]);

        if ($specificTask) {
            $deadline = $specificTask->getDateDeadline();
            $diff = $today->diff($deadline);
            $days = (int)$diff->format('%r%a');
            $dateHuman = $days === 0 ? "Aujourd'hui" : ($days > 0 ? "Dans $days jours" : "Il y a " . abs($days) . " jours");
            
            $messagesDeadlines = '<span class="sidebar-label">' . $specificTask->getName() . '</span> 
                                <span class="sidebar-value" style="color:#f87171;">' . $dateHuman . '</span>';
        }

        if ($specificTask2) {
            $deadline = $specificTask2->getDateDeadline();
            $diff = $today->diff($deadline);
            $days = (int)$diff->format('%r%a');
            $dateHuman = $days === 0 ? "Aujourd'hui" : ($days > 0 ? "Dans $days jours" : "Il y a " . abs($days) . " jours");
            
            $messagesDeadlines2 = '<span class="sidebar-label">' . $specificTask2->getName() . '</span> 
                                 <span class="sidebar-value" style="color:#fbbf24;">' . $dateHuman . '</span>';
        }

        return $this->render('task/index.html.twig', [
            'task'              => $tasks,
            'count'             => count($tasks),
            'countTotal'        => $countTotal,
            'countEnd'          => $countEnd,
            'countProgress'     => $countProgress,
            'countUrgence'      => $countUrgence,
            'messages'          => $messages,
            'messagesDeadlines' => $messagesDeadlines,
            'messagesDeadlines2'=> $messagesDeadlines2, // On passe bien la variable ici
            'countGroup1'       => $countGroup1,
            'countGroup2'       => $countGroup2,
            'countGroup3'       => $countGroup3,
        ]);
    }

    #[Route('/new', name: 'app_task_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $task->setDateCreation(new \DateTime());
        $form = $this->createForm(TaskCreateFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();
            $this->addFlash('success', "La tâche a été bien ajoutée en base");
            return $this->redirectToRoute('app_task');
        }

        return $this->render('task/new.html.twig', [
            'taskForm' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_task_edit')]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskCreateFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', "La tâche a été bien modifiée en base");
            return $this->redirectToRoute('app_task');
        }

        return $this->render('task/new.html.twig', [
            'taskForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_task_delete')]
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($task);
        $entityManager->flush();
        $this->addFlash('success', "La tâche a été bien supprimée en base");
        return $this->redirectToRoute('app_task');
    }

    #[Route('/assign-groups', name: 'app_task_assign_groups')]
    public function assignGroups(EntityManagerInterface $entityManager, TaskRepository $taskRepo): Response
    {
        $groupRepo = $entityManager->getRepository(TaskGroup::class);
        $tasks = $taskRepo->findAll();
        $allGroups = $groupRepo->findAll();

        if (empty($allGroups)) {
            $this->addFlash('danger', "Aucun groupe n'existe en base de données !");
            return $this->redirectToRoute('app_task');
        }

        $count = 0;
        foreach ($tasks as $task) {
            $randomGroup = $allGroups[array_rand($allGroups)];
            $task->setTaskGroup($randomGroup);
            $count++;
        }

        $entityManager->flush();
        $this->addFlash('success', "$count tâches ont été réattribuées aléatoirement.");

        return $this->redirectToRoute('app_task');
    }
}