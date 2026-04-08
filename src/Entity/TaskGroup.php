<?php

namespace App\Entity;

use App\Repository\TaskGroupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskGroupRepository::class)]
class TaskGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column (name: 'taskgroup_id')]
    private ?int $id = null;

    #[ORM\Column(name: 'taskgroup_name',length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'taskgroup_description',length: 255)]
    private ?string $description = null;

    #[ORM\Column(name: 'taskgroup_status',length: 255)]
    private ?string $status = null;

    #[ORM\Column(name: 'taskgroup_archivedTask')]
    private ?bool $archivedTask = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function isArchivedTask(): ?bool
    {
        return $this->archivedTask;
    }

    public function setArchivedTask(bool $archivedTask): static
    {
        $this->archivedTask = $archivedTask;

        return $this;
    }
}
