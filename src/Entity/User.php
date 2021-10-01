<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * @UniqueEntity(fields="username", message="Username is already taken")
 * @UniqueEntity(fields="email", message="Email is already taken")
 */
class User
{
    public const DEFAULT_ROLE_ID = 1;
    public const ADMIN_ROLE_ID = 2;
    public const DEFAULT_STATUS_ID = 1;
    public const CONFIRMED_STATUS_ID = 2;
    public const BANNED_STATUS_ID = 3;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotBlank(
     *     message = "Username is required."
     * )
     * @Assert\Length(min=3, minMessage="Username must be longer than 3 characters.")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(
     *     message = "Email is required."
     * )
     * @Assert\Email(
     *     message = "Email is not valid."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=256)
     * @Assert\NotBlank(
     *     message = "Password is required."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $wallet;

    /**
     * @ORM\Column(type="integer")
     */
    private $role_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $status_id;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $rewarded_at;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     */
    private $role;

    /**
     * @ORM\ManyToOne(targetEntity="UserStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private $status;

    public function __construct()
    {
        $this->setWallet(0);
        $this->setCreatedAt(new \DateTimeImmutable("now"));
        $this->setUpdatedAt(new \DateTimeImmutable("now"));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getWallet(): ?int
    {
        return $this->wallet;
    }

    public function setWallet(int $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getRoleId(): ?int
    {
        return $this->role_id;
    }

    public function setRoleId(int $role_id): self
    {
        $this->role_id = $role_id;

        return $this;
    }

    public function getStatusId(): ?int
    {
        return $this->status_id;
    }

    public function setStatusId(int $status_id): self
    {
        $this->status_id = $status_id;

        return $this;
    }

    public function getRewardedAt(): ?\DateTimeImmutable
    {
        return $this->rewarded_at;
    }

    public function setRewardedAt(?\DateTimeImmutable $rewarded_at): self
    {
        $this->rewarded_at = $rewarded_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role): void
    {
        $this->role = $role;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
    }
}
