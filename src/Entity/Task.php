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
    /* Getter et setter pour id, tasktype, taskgroup, name, details, dateCreation, dateDeadline, status
     * Les getters permettent de récupérer les valeurs des propriétés d'une tâche
     * Les setters permettent de définir ou modifier les valeurs des propriétés d'une tâche
     * Ces méthodes sont utilisées dans les contrôleurs et les formulaires pour manipuler les données des tâches
     */
    /**
     * Get the value of id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /* Getter et setter pour tasktype
     * Permet de récupérer ou définir le type d'une tâche (relation ManyToOne avec TaskType)
     * @return TaskType|null
     */
    public function getTaskType(): ?TaskType
    {
        return $this->tasktype;
    }
    /* Setter pour tasktype
     * Permet de définir le type d'une tâche en lui associant une instance de TaskType
     * @param TaskType|null $taskType
     * @return static
     */
    public function setTaskType(?TaskType $taskType): static
    {
        $this->tasktype = $taskType;
        return $this;
    }
    /* Getter et setter pour taskgroup
     * Permet de récupérer ou définir le groupe d'une tâche (relation ManyToOne avec TaskGroup)
     * @return TaskGroup|null
     */
    public function getTaskGroup(): ?TaskGroup
    {
        return $this->taskgroup;
    }
    /* Setter pour taskgroup
     * Permet de définir le groupe d'une tâche en lui associant une instance de TaskGroup
     * @param TaskGroup|null $taskGroup
     * @return static
     */
    public function setTaskGroup(?TaskGroup $taskGroup): static
    {
        $this->taskgroup = $taskGroup;
        return $this;
    }
    /**
     * Get the value of name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
    /* Setter pour name
     * Permet de définir le nom d'une tâche
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    /* Getter et setter pour details
     * Permet de récupérer ou définir les détails d'une tâche
     * @return string|null
     */
    public function getDetails(): ?string
    {
        return $this->details;
    }
    /* Setter pour details
     * Permet de définir les détails d'une tâche
     * @param string $details
     * @return static
     */
    public function setDetails(string $details): static
    {
        $this->details = $details;
        return $this;
    }
    /* Getter et setter pour dateCreation
    * Permet de récupérer ou définir la date de création d'une tâche
    * @return \DateTime|null
    */
    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }
    /* Setter pour dateCreation
     * Permet de définir la date de création d'une tâche
     * @param \DateTime $dateCreation
     * @return static
     */
    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }
    /* Getter et setter pour dateDeadline
     * Permet de récupérer ou définir la date limite d'une tâche
     * @return \DateTime|null
     */
    public function getDateDeadline(): ?\DateTime
    {
        return $this->dateDeadline;
    }
    /* Setter pour dateDeadline
     * Permet de définir la date limite d'une tâche
     * @param \DateTime $dateDeadline
     * @return static
     */
    public function setDateDeadline(\DateTime $dateDeadline): static
    {
        $this->dateDeadline = $dateDeadline;
        return $this;
    }
    /* Getter et setter pour status
     * Permet de récupérer ou définir le statut d'une tâche (ex: En cours, Terminée, Urgence)
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }
    /* Setter pour status
     * Permet de définir le statut d'une tâche
     * @param string $status
     * @return static
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }
}