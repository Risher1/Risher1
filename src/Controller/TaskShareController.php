<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\GuestUserType;
use App\Form\TaskShareType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class TaskShareController extends AbstractController
{
    #[Route('/task/share_page/{id}', name: 'app_task_share_page', defaults: ['id' => null])]
    public function index(?Task $task, Request $request, UserRepository $userRepository, TaskRepository $taskRepository): Response
    {
        if (!$task) {
            $task = $taskRepository->findOneBy([], ['id' => 'DESC']); 
        }
      
        // On doit aussi récupérer les tâches reçues ici pour l'affichage initial
        $userConnected = $this->getUser();
        $receivedTasks = [];
        if ($userConnected instanceof User) {
            $receivedTasks = $taskRepository->createQueryBuilder('t')
                ->join('t.sharedWith', 'u')
                ->where('u.id = :userId')
                ->setParameter('userId', $userConnected->getId())
                ->getQuery()
                ->getResult();
        }

        return $this->render('task_share/index.html.twig', [
            'task'          => $task,
            'users'         => $userRepository->findAll(),
            'shareForm'     => $this->createForm(TaskShareType::class)->createView(),
            'receivedTasks' => $receivedTasks,
           'inviteForm' => $this->createForm(GuestUserType::class)->createView()
        ]);
    }

    #[Route('/task/invite', name: 'app_task_invite')]
    public function invite(Request $request, MailerInterface $mailer): Response
    {
        $inviteForm = $this->createForm(GuestUserType::class);
        $inviteForm->handleRequest($request);

        if ($inviteForm->isSubmitted() && $inviteForm->isValid()) {
            $maildestination = $inviteForm->get('email')->getData();
            $email = (new TemplatedEmail())
                ->from('hello@example.com')
                ->to($maildestination)
                ->subject('Objet du mail')
                ->htmlTemplate('task_share/guest.html.twig')
                ->context(['name'=> 'Thomas']);

            $mailer->send($email);
            $this->addFlash('success', "L'invitation a été envoyée !");
            
            // Correction : on redirige vers l'index pour éviter l'erreur de variable
            return $this->redirectToRoute('app_task_share_page');
        }

        return $this->render('task_share/guest_form.html.twig', [
            'inviteForm' => $inviteForm->createView(),
        ]);
    }

   #[Route('/task/share/{id}', name: 'app_task_share')]
    public function sharedTaskUser(Task $task, Request $request, UserRepository $userRepository, TaskRepository $taskRepository, EntityManagerInterface $entityManager, MailerInterface $mailer): Response { 
    $userConnected = $this->getUser();

    // 1. Vérification de la connexion
    if (!$userConnected instanceof User) {
        $this->addFlash('danger', "Vous devez être connecté pour partager une tâche.");
        return $this->redirectToRoute('app_login');
    }

    // 2. Création du formulaire de partage
    $shareForm = $this->createForm(TaskShareType::class);
    $shareForm->handleRequest($request);

    if ($shareForm->isSubmitted() && $shareForm->isValid()) {
        // 3. Récupération des données (tableau car data_class => null)
        $data = $shareForm->getData();
        
        /** @var Task $selectedTask */
        $selectedTask = $data['task']; // L'entité Task choisie dans le select
        /** @var User $destinataire */
        $destinataire = $data['user']; // L'entité User choisie dans le select
        $messageCustom = $data['taskSharedMessage']; // Le message optionnel

        if ($destinataire) {
            try {
                // 4. Liaison ManyToMany : On ajoute l'utilisateur à la tâche
                $selectedTask->addSharedWith($destinataire);
                
                // 5. Sauvegarde en base de données
                $entityManager->persist($selectedTask);
                $entityManager->flush();

                // 6. Préparation et envoi de l'email
                $email = (new TemplatedEmail())
                    ->from('hello@example.com') 
                    ->to($destinataire->getEmail())
                    ->subject('Collaboration sur la tâche : ' . $selectedTask->getName())
                    ->htmlTemplate('task_share/share.html.twig')
                    ->context([
                        'sender'  => $userConnected,
                        'task'    => $selectedTask,
                        'message' => $messageCustom,
                    ]);

                $mailer->send($email);

                $this->addFlash('success', "La tâche '" . $selectedTask->getName() . "' a été partagée avec succès !");
            } catch (\Exception $e) {
                $this->addFlash('danger', "Erreur lors du partage : " . $e->getMessage());
            }
        } else {
            $this->addFlash('warning', "Veuillez sélectionner un collaborateur valide.");
        }
    
        // Redirection vers la page de partage (on garde l'ID de la tâche actuelle)
        return $this->redirectToRoute('app_task_share_page', ['id' => $task->getId()]);
    }

        // 7. Si le formulaire n'est pas valide ou juste affiché 
        // On récupère les tâches reçues pour ne pas casser l'affichage de la vue
        $receivedTasks = $taskRepository->createQueryBuilder('t')
        ->join('t.sharedWith', 'u')
        ->where('u.id = :userId')
        ->setParameter('userId', $userConnected->getId())
        ->getQuery()
        ->getResult();

        return $this->render('task_share/index.html.twig', [
            'task'          => $task,
            'shareForm'     => $shareForm->createView(),
            'inviteForm'    => $this->createForm(GuestUserType::class)->createView(),
            'users'         => $userRepository->findAll(),
            'receivedTasks' => $receivedTasks
        ]);
    }

    #[Route('/task/accept/{id}', name: 'app_task_accept')]
    public function acceptTask(Task $task, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($user) {
            $task->addSharedWith($user);
            $em->flush();
            $this->addFlash('success', "Vous collaborez sur : " . $task->getName());
        }
        return $this->redirectToRoute('app_task_share_page', ['id' => $task->getId()]);
    }

    #[Route('/task/reject/{id}', name: 'app_task_reject')]
    public function rejectTask(Task $task): Response
    {
        $this->addFlash('info', "Refusé : " . $task->getName());
        return $this->redirectToRoute('app_home');
    }
}