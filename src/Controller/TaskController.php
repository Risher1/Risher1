<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\TaskType;
use App\Entity\TaskGroup;
use App\Repository\TaskRepository;
use App\Form\TaskCreateFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task')]
    //#[IsGranted('ROLE_USER' , message: "Vous n'avez pas les droits pour accéder à cette page !")]
    public function index(Request $request, TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {   
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        // 1. Gestion des filtres et recherche
        $statusFilter = $request->query->get('status'); 
        $searchQuery = $request->query->get('q');

        if (!empty($searchQuery)) {
            // Si on recherche quelque chose, on utilise ta méthode de recherche
            $tasks = $taskRepository->findBySearch($searchQuery);
            
            // Optionnel : Si on veut AUSSI filtrer par statut les résultats de la recherche
            if (!empty($statusFilter)) {
                // On filtre la collection PHP résultante
                $tasks = array_filter($tasks, fn($t) => $t->getStatus() === $statusFilter);
            }
        } elseif (!empty($statusFilter)) {
            // Sinon, si on a juste un clic sur un bouton de statut
            $tasks = $taskRepository->findBy(['status' => $statusFilter]);
        } else {
            // Sinon, liste complète
            $tasks = $taskRepository->findAll();
        }
        // Stats habituelles pour l'affichage du nombre de tâches par statut
        $countTotal    = $taskRepository->count([]);
        $countEnd      = $taskRepository->count(['status' => 'Terminée']);
        $countProgress = $taskRepository->count(['status' => 'En cours']);
        $countUrgence  = $taskRepository->count(['status' => 'Urgence']);


        // Affichage du groupe de taches et le nombre de taches associées
        $groupRepo = $entityManager->getRepository(TaskGroup::class);
        $allGroups = $groupRepo->findAll();

        //Affichage de la date d'échéance proche
        
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

        // On passe toutes les variables à Twig pour affichage
        return $this->render('task/index.html.twig', [
            'task'              => $tasks,
            'count'             => count($tasks),
            'countTotal'        => $countTotal,
            'countEnd'          => $countEnd,
            'countProgress'     => $countProgress,
            'countUrgence'      => $countUrgence,
            'messagesDeadlines' => $messagesDeadlines,
            'messagesDeadlines2'=> $messagesDeadlines2, 
            'allGroups'         => $allGroups
            
        ]);
    }


    // fonction de création d'une tâche avec formulaire Symfony 
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


    // fonction d'édition d'une tâche avec formulaire Symfony
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


    // fonction de suppression d'une tâche
    #[Route('/delete/{id}', name: 'app_task_delete', methods: ['GET', 'POST'])]
    public function delete(Task $task, EntityManagerInterface $entityManager): Response
    {
        // Sécurité
        if (!$this->isGranted('ROLE_ADMIN') && $task->getUser() !== $this->getUser()) {
            $this->addFlash('danger', "Vous n'avez pas les droits !");
            return $this->redirectToRoute('app_task'); 
        }

        $entityManager->remove($task);
        $entityManager->flush();

        $this->addFlash('success', "La tâche a bien été supprimée.");

        // C'est ici que tu rediriges vers la liste
        return $this->redirectToRoute('app_task'); 
    }


}