<?php
 
namespace App\Controller;
 
use App\Entity\Task;
use App\Entity\TaskGroup;
use App\Entity\User;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
 
final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(TaskRepository $taskRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
 
        //Stats générales
        $countTotal    = $taskRepository->count([]);
        $countEnd      = $taskRepository->count(['status' => 'Terminée']);
        $countProgress = $taskRepository->count(['status' => 'En cours']);
 
        // Tâches partagées (celles qui ont au moins un sharedWith)
        $countShared = $taskRepository->createQueryBuilder('t')
            ->select('COUNT(DISTINCT t.id)')
            ->join('t.sharedWith', 'u')
            ->getQuery()
            ->getSingleScalarResult();
 
        // Progression par groupe
        $groupRepo = $entityManager->getRepository(TaskGroup::class);
        $allGroups = $groupRepo->findAll();
 
        $groupsProgress = [];
        foreach ($allGroups as $group) {
            $totalInGroup = $taskRepository->count(['taskgroup' => $group]);
            if ($totalInGroup === 0) {
                continue;
            }
            $doneInGroup = $taskRepository->count(['taskgroup' => $group, 'status' => 'Terminée']);
            $percent = (int) round(($doneInGroup / $totalInGroup) * 100);
 
            $groupsProgress[] = [
                'name'    => $group->getName(),
                'total'   => $totalInGroup,
                'done'    => $doneInGroup,
                'percent' => $percent,
            ];
        }
 
        // Activité hebdomadaire (tâches créées par jour cette semaine)
        $weekData = [];
        $daysFr = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
        for ($i = 6; $i >= 0; $i--) {
            $day = new \DateTime("-$i days");
            $dayLabel = $daysFr[(int)$day->format('w')];
 
            $count = $taskRepository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->where('t.dateCreation = :day')
                ->setParameter('day', $day->format('Y-m-d'))
                ->getQuery()
                ->getSingleScalarResult();
 
            $weekData[] = [
                'day'       => $dayLabel,
                'count'     => (int) $count,
                'highlight' => ($i === 0),
            ];
        }
 
        // Alertes d'échéance (tâches non terminées avec deadline dans les 7 prochains jours)
        $today       = new \DateTime('today');
        $in7days     = (clone $today)->modify('+7 days');
 
        $upcomingTasks = $taskRepository->createQueryBuilder('t')
            ->where('t.dateDeadline >= :today')
            ->andWhere('t.dateDeadline <= :in7days')
            ->andWhere('t.status != :done')
            ->setParameter('today', $today)
            ->setParameter('in7days', $in7days)
            ->setParameter('done', 'Terminée')
            ->orderBy('t.dateDeadline', 'ASC')
            ->getQuery()
            ->getResult();
 
        $alerts = [];
        foreach ($upcomingTasks as $task) {
            $deadline = $task->getDateDeadline();
            $diff     = $today->diff($deadline);
            $days     = (int) $diff->format('%r%a');
 
            if ($days === 0) {
                $dateHuman = "Aujourd'hui";
                $level     = 'danger';
            } elseif ($days === 1) {
                $dateHuman = 'Demain';
                $level     = 'danger';
            } elseif ($days <= 3) {
                $dateHuman = "$days jours";
                $level     = 'warning';
            } else {
                $dateHuman = "$days jours";
                $level     = 'info';
            }
 
            $alerts[] = [
                'name'      => $task->getName(),
                'dateHuman' => $dateHuman,
                'level'     => $level,
            ];
        }
 
        // Tâches partagées récentes 
        $recentShared = $taskRepository->createQueryBuilder('t')
            ->join('t.sharedWith', 'u')
            ->orderBy('t.dateCreation', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
 
        return $this->render('dashboard/index.html.twig', [
            'countTotal'     => $countTotal,
            'countEnd'       => $countEnd,
            'countProgress'  => $countProgress,
            'countShared'    => $countShared,
            'groupsProgress' => $groupsProgress,
            'weekData'       => $weekData,
            'alerts'         => $alerts,
            'recentShared'   => $recentShared,
        ]);
    }
 
  
}
 