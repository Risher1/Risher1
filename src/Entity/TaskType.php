<?php

namespace App\Entity;

use App\Repository\TaskTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskTypeRepository::class)]
class TaskType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'tasktype_id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'tasktype_type',length: 255)]
    private ?string $type = null;

    /**
     * Getteur de l'id du type de tâche
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Setteur de l'id du type de tâche
     * @param int $id
     * @return TaskType
     */
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
    /**
     * Getteur du type de tâche
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }
    /**
     * Setteur du type de tâche
     * @param string $type
     * @return TaskType
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
