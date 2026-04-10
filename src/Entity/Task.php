<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use App\Entity\TaskType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'task_id', type: 'integer')]
    private ?int $id = null;

    // Relation avec TaskType : On place l'attribut juste au-dessus de la variable correspondante
    #[ORM\ManyToOne(targetEntity: TaskType::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'tasktype_id', referencedColumnName: 'tasktype_id', nullable: true)]
    private ?TaskType $tasktype = null;

    // Relation avec TaskGroup 
    #[ORM\ManyToOne(targetEntity: TaskGroup::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'taskgroup_id', referencedColumnName: 'taskgroup_id', nullable: true)]
    private ?TaskGroup $taskgroup = null;
  

    #[ORM\Column(name: 'task_name', length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'task_details', type: Types::TEXT)]
    private ?string $details = null;

    #[ORM\Column(name: 'task_datecreation', type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(name: 'datedeadline', type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDeadline = null;

    #[ORM\Column(name: 'task_status', length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTaskType(): ?TaskType
    {
        return $this->tasktype;
    }

    public function setTaskType(?TaskType $taskType): static
    {
        $this->tasktype = $taskType;
        return $this;
    }
    // Getter et setter pour taskgroup
    public function getTaskGroup(): ?TaskGroup
    {
        return $this->taskgroup;
    }

    public function setTaskGroup(?TaskGroup $taskGroup): static
    {
        $this->taskgroup = $taskGroup;
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

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): static
    {
        $this->details = $details;
        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    public function getDateDeadline(): ?\DateTime
    {
        return $this->dateDeadline;
    }

    public function setDateDeadline(\DateTime $dateDeadline): static
    {
        $this->dateDeadline = $dateDeadline;
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
}