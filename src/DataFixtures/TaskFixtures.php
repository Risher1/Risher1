<?php
namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\TaskType;
use App\Entity\User;
use App\Entity\TaskGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\DBAL\Driver\Mysqli\Initializer;
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
        $user->setDateCreateAt(new \DateTime()); 
        $user->setPassword($this->passwordHasher->hashPassword($user, 'Qwerty123@#{}'));
        $user->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
         
        // 3.1 Créer d'autres utilisateurs
        $usersData = [
        ['email' => 'admin@todo.com', 'username' => 'AdminMaster', 'firstname' => 'Jean', 'pseudo' => 'LeBoss', 'birthday' => '15/05/1985', 'roles' => ['ROLE_ADMIN']],
        ['email' => 'alice@example.com', 'username' => 'AliceL', 'firstname' => 'Alice', 'pseudo' => 'AliTech', 'birthday' => '22/11/1992', 'roles' => ['ROLE_USER']],
        ['email' => 'bob@gmail.com', 'username' => 'Boby78', 'firstname' => 'Robert', 'pseudo' => 'Bobby', 'birthday' => '03/01/2000', 'roles' => ['ROLE_USER']],
        ['email' => 'claire.dev@outlook.fr', 'username' => 'Clairette', 'firstname' => 'Claire', 'pseudo' => 'ClaireDev', 'birthday' => '30/08/1995', 'roles' => ['ROLE_USER']],
        ['email' => 'thomas@test.com', 'username' => 'TomTom', 'firstname' => 'Thomas', 'pseudo' => 'TomLeBricoleur', 'birthday' => '12/03/1998', 'roles' => ['ROLE_USER']],
        ];
        foreach ($usersData as $data) {
            $user = (new User())
            ->setEmail($data['email'])
            ->setUsername($data['username'])
            ->setFirstname($data['firstname'])
            ->setPseudo($data['pseudo'])
            ->setBirthday(\DateTime::createFromFormat('d/m/Y', $data['birthday']))
            ->setDateCreateAt(new \DateTime())
            ->setRoles($data['roles']);

            $user->setPassword($this->passwordHasher->hashPassword($user, 'Qwerty123@#{}'));

            $manager->persist($user);
               //On recupère le tableau d'utilisateur 
            $allUsers[] = $user;
        }
         
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

            // Lier une tachen a un groupe si le groupe existe
            if (isset($groups[$groupName])) {
                $task->setTaskgroup($groups[$groupName]);
            }

            $manager->persist($task);
            // On recupère le tableau de tache 
            $allTasks[] = $task;
           
        }
        // On lit un utilisateur a une tache 
        // On verifie si l'utilisateur est déja lié a une tache 
        foreach ($allTasks as $task) {
            // 1. On définit le propriétaire (Actif)
            $owner = $allUsers[array_rand($allUsers)];
            $task->setUser($owner);
            $task->addSharedWith($owner); // Il est dans la liste

        // 2. ON ajoute un collaborateur (Invité)  !
        // On cherche un utilisateur qui n'est pas le propriétaire
        foreach ($allUsers as $potentialGuest) {
            if ($potentialGuest !== $owner) {
                $task->addSharedWith($potentialGuest);
                break; // On en ajoute un seul pour tester
            }
        }

        $manager->persist($task);
    }

        $manager->flush();
    }
    
}