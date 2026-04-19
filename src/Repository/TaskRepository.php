<?php
 
namespace App\Repository;
 
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
 
/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }
 
    /**
     * Recherche des tâches par nom ou détails (recherche partielle, insensible à la casse)
     *
     * @return Task[]
     */
    public function findBySearch(string $query): array
    {
        $qb = $this->createQueryBuilder('t');
        
        // On découpe la saisie en mots séparés
        $words = array_filter(explode(' ', trim($query)));

        foreach ($words as $i => $word) {
            $qb->andWhere(
                $qb->expr()->orX(
                    "LOWER(t.name) LIKE LOWER(:word$i)",
                    "LOWER(t.details) LIKE LOWER(:word$i)"
                )
            )
            ->setParameter("word$i", '%' . $word . '%');
        }

        return $qb
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}