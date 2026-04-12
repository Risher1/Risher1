<?php

namespace App\Entity;

use App\Repository\TaskGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskGroupRepository::class)]
class TaskGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'taskgroup_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'taskgroup_name', length: 255)]
    private ?string $name = null;


    #[ORM\Column(name: 'taskgroup_description', length: 255, nullable: true)]
    private ?string $description = null;

 
    #[ORM\Column(name: 'taskgroup_status', length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(name: 'taskgroup_archivedTask', nullable: true)]
    private ?bool $archivedTask = null;
    /**
     * Getteur de l'id du groupe de tâches
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /* Setteur de l'id du groupe de tâches
     * @param int $id
     * @return TaskGroup
     */
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Getteur du nom du groupe de tâches
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Setteur du nom du groupe de tâches
     * @param string $name
     * @return TaskGroup
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Getteur de la description du groupe de tâches
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Setteur de la description du groupe de tâches
     * @param string $description
     * @return TaskGroup
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Getteur du statut du groupe de tâches
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Setteur du statut du groupe de tâches
     * @param string $status
     * @return TaskGroup
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Getteur de l'état d'archivage du groupe de tâches
     * @return bool|null
     */
    public function isArchivedTask(): ?bool
    {
        return $this->archivedTask;
    }

    /**
     * Setteur de l'état d'archivage du groupe de tâches
     * @param bool $archivedTask
     * @return TaskGroup
     */
    public function setArchivedTask(bool $archivedTask): static
    {
        $this->archivedTask = $archivedTask;

        return $this;
    }
}
