<?php

namespace App\Twig\Runtime;

use App\Entity\User;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        // Inject any services you need here
        private RoleHierarchyInterface $roleHierarchy
    )
    {
        // Inject dependencies if needed
    }
        //definie une méthode pour vérifier les rôles d'un utilisateur
    public function hasRole(UserInterface $user, string $role): bool
    {
        // Récupère les rôles hiérarchiques de l'utilisateur
        $roles = $this->roleHierarchy->getReachableRoleNames($user->getRoles());

        // Vérifie si le rôle demandé est dans la liste des rôles de l'utilisateur
        return in_array($role, $roles, true);if (!$user || $user->getId() !== $id) {
            throw $this->createAccessDeniedException();
        }
    }
}
