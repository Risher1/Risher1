<?php
namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\TaskType;
use App\Entity\TaskGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $arraytask = [
            ['Conception BDD MERISE',         'modèle conceptuel, MERISE...',           '12/03/2026', '15/03/2026', 'Terminée'],
            ['Intégration Symfony Auth',       'installation + commandes sécurité',      '27/03/2026', '31/03/2026', 'Terminée'],
            ['Maquettes HTML/CSS Bootstrap',   'Figma + intégration Bootstrap',          '31/03/2026', '20/04/2026', 'En cours'],
            ['Config Docker Symfony',          'mise en place environnement Docker',     '01/04/2026', '04/04/2026', 'En cours'],
            ['Rédiger les tests unitaires',    'tests PHPUnit sur les fonctionnalités',  '02/04/2026', '05/04/2026', 'En cours'],
            ['Dashboard stats page',           'création page statistiques',             '03/04/2026', '06/04/2026', 'Urgence'],
            ['Déploiement o2switch',           'mise en ligne sur hébergement',          '15/03/2026', '20/03/2026', 'Terminée'],
            ['Dictionnaire de données',        'définition des champs BDD',              '20/03/2026', '25/03/2026', 'Terminée'],
            ['Sécurité CSRF Symfony',          'protection des formulaires',             '04/04/2026', '08/04/2026', 'Urgence'],
            ['Mise en ligne finale',           'déploiement final du projet',            '05/04/2026', '10/04/2026', 'En cours'],
        ];

        foreach ($arraytask as [$name, $details, $created, $deadline, $status]) {
            $task = new Task();
            $task->setName($name);
            $task->setDetails($details);
            $task->setDateCreation(\DateTime::createFromFormat('d/m/Y', $created));
            $task->setDateDeadline(\DateTime::createFromFormat('d/m/Y', $deadline));
            $task->setStatus($status);
            $manager->persist($task);
        }

        foreach (['Urgent', 'Personnel', 'Professionnel', 'Loisir'] as $typeName) {
            $type = new TaskType();
            $type->setType($typeName);
            $manager->persist($type);
        }

        foreach (['Projet Symfony', 'Maquettes UI/UX', 'Sécurité'] as $name) {
            $groupe = new TaskGroup();
            $groupe->setName($name);
            $manager->persist($groupe);
        }

        $manager->flush();
    }

    
}