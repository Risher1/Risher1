<?php
namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\TaskType;
use App\Entity\User;
use App\Entity\TaskGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TaskFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

   public function load(ObjectManager $manager): void
    {
        // 1. Créer les Groupes
        $groups = [];
        foreach (['Projet Symfony', 'Maquettes UI/UX', 'Sécurité'] as $name) {
            $groupe = new TaskGroup();
            $groupe->setName($name);
            $manager->persist($groupe);
            $groups[$name] = $groupe;
        }

        // 2. Créer les Types
        $types = [];
        foreach (['Urgent', 'Personnel', 'Professionnel', 'Loisir'] as $typeName) {
            $type = new TaskType();
            $type->setType($typeName);
            $manager->persist($type);
            $types[$typeName] = $type;
        }

        // 3. Créer l'Utilisateur
        $user = new User();
        $user->setEmail('hello@example.com');
        $user->setUsername('Risher');
        $user->setFirstname('Aris');
        $user->setPseudo('Arisrisher');
        $user->setBirthday(\DateTime::createFromFormat('d/m/Y', '12/03/1998'));
        $user->setDateCreateAt(new \DateTime()); // Assure-toi que ce champ est rempli
        $user->setPassword($this->passwordHasher->hashPassword($user, 'Qwerty123@#{}'));
        $manager->persist($user);

        // 4. Créer les Tâches (Note l'ajout du groupe en 6ème position)
        $arraytask = [
            ['Conception BDD MERISE', 'modèle conceptuel, MERISE...', '12/03/2026', '15/03/2026', 'Terminée', 'Projet Symfony'],
            ['Intégration Symfony Auth', 'installation + commandes sécurité', '27/03/2026', '31/03/2026', 'Terminée', 'Sécurité'],
            ['Maquettes HTML/CSS Bootstrap', 'Figma + intégration Bootstrap', '31/03/2026', '20/04/2026', 'En cours', 'Maquettes UI/UX'],
            ['Config Docker Symfony', 'mise en place environnement Docker', '01/04/2026', '04/04/2026', 'En cours', 'Projet Symfony'],
            ['Rédiger les tests unitaires', 'tests PHPUnit sur les fonctionnalités', '02/04/2026', '05/04/2026', 'En cours', 'Projet Symfony'],
            ['Dashboard stats page', 'création page statistiques', '03/04/2026', '06/04/2026', 'Urgence', 'Projet Symfony'],
            ['Déploiement o2switch', 'mise en ligne sur hébergement', '15/03/2026', '20/03/2026', 'Terminée', 'Projet Symfony'],
            ['Dictionnaire de données', 'définition des champs BDD', '20/03/2026', '25/03/2026', 'Terminée', 'Projet Symfony'],
            ['Sécurité CSRF Symfony', 'protection des formulaires', '04/04/2026', '08/04/2026', 'Urgence', 'Sécurité'],
            ['Mise en ligne finale', 'déploiement final du projet', '05/04/2026', '10/04/2026', 'En cours', 'Projet Symfony'],
        ];

        foreach ($arraytask as [$name, $details, $created, $deadline, $status, $groupName]) {
            $task = new Task();
            $task->setName($name);
            $task->setDetails($details);
            $task->setDateCreation(\DateTime::createFromFormat('d/m/Y', $created));
            $task->setDateDeadline(\DateTime::createFromFormat('d/m/Y', $deadline));
            $task->setStatus($status);

            // Lier au groupe
            if (isset($groups[$groupName])) {
                $task->setTaskgroup($groups[$groupName]);
            }

            // Optionnel : lier à l'user si ton entité Task a une relation setUser()
            // $task->setUser($user);

            $manager->persist($task);
        }

        $manager->flush();
    }
    
}