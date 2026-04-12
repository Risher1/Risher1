<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'role_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'role_name', length: 60)]
    private ?string $role = null;
    /**
     * Getteur de l'id du role
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Le setter de l'id 
     * @param int $id
     */
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
    /**
    * Getteur du role
    * @return string|null
    */
    public function getRole(): ?string
    {
        return $this->role;
    }
    /**
     * Le setter du role
     * @param string $role
     */
    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }
}
