<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  
    public function __construct()
    {
        // On définit la date de création automatiquement à "maintenant"
        $this->dateCreateAt = new \DateTime();
        $this->roles = [];
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(name: 'user_email', length: 180)]
    private ?string $email = null;
    #[ORM\Column(name: 'user_username', length: 160)]
    private ?string $username = null;
    #[ORM\Column(name: 'user_firstname', length: 160)]
    private ?string $firstname = null;
    #[ORM\Column(name: 'user_pseudo', length: 160)]
    private ?string $pseudo = null;
    #[ORM\Column(name: 'user_birthday', type: Types::DATE_MUTABLE)]
    private ?\DateTime $birthday = null;
    #[ORM\Column(name: 'user_datecreation', type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateCreateAt = null;

    /* Getter et setter pour username
     * Permet de récupérer ou définir le nom d'utilisateur
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /* Setter pour username
     * Permet de définir le nom d'utilisateur
     * @param string $username
     * @return static
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;
      /*pour le chainage des setters, on retourne l'instance courante de 
      l'utilisateur ($this) pour permettre d'enchaîner les appels de setters*/
        return $this; 
    }
    /* Getter et setter pour firstname
     * Permet de récupérer ou définir le prénom de l'utilisateur
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }
   /* Setter pour firstname
     * Permet de définir le prénom de l'utilisateur
     * @param string $firstname
     * @return static
     */
    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;
        return $this;
    }
    /* Getter et setter pour pseudo
     * Permet de récupérer ou définir le pseudo de l'utilisateur
     * @return string|null
     */
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }
    /* Setter pour pseudo
    * Permet de définir le pseudo de l'utilisateur
    * @param string $pseudo
    * @return static
    */
    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;
        return $this;
    }
    
    /* Getter et setter pour birthday
     * Permet de récupérer ou définir la date de naissance de l'utilisateur
     * @return \DateTime|null
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }
  

    /* Setter pour birthday
     * Permet de définir la date de naissance de l'utilisateur
     * @param \DateTime $birthday
     * @return static
     */
    public function setBirthday(\DateTime $birthday): static
    {
        $this->birthday = $birthday;

        return $this;
    }
    /* Getter et setter pour dateCreation
     * Permet de récupérer ou définir la date de création du compte utilisateur
     * @return \DateTime|null
     */
    public function getDateCreateAt(): ?\DateTime
    {
        return $this->dateCreateAt;
    }
    /* Setter pour dateCreation
     * Permet de définir la date de création du compte utilisateur
     * @param \DateTime $dateCreation
     * @return static
     */
    public function setDateCreateAt(\DateTime $dateCreateAt): static
    {
        $this->dateCreateAt = $dateCreateAt;
        return $this;
    }
    /**
     * le registre des rôles est un tableau de chaînes de caractères
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * le mot de passe est stocké sous forme de chaîne de caractères hachée
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    private bool $isVerified = false;

    /**
     * Getteur de l'id de l'utilisateur
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * Getteur de l'email de l'utilisateur
     * @return string|null
     */

    public function getEmail(): ?string
    {
        return $this->email;
    }
    /**
    * Setteur de l'email de l'utilisateur
    * @param string $email
    * @return static
    */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     * @return string
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
