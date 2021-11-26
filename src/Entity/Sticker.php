<?php

namespace App\Entity;

use App\Repository\StickerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=StickerRepository::class)
 * @ORM\Table(name="stickers")
 */
class Sticker
{
    public const MAX_CHANCE = 100_000;

    /**
     * @Groups("userItems")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @Groups("userItems")
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(
     *     message="sticker.name.required"
     * )
     */
    private ?string $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(
     *     message="sticker.coefficient.required"
     * )
     */
    private ?int $coefficient;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $chance;

    /**
     * @Groups("userItems")
     * @ORM\Column(type="string", length=64)
     */
    private ?string $path;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private \DateTimeImmutable $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private \DateTimeImmutable $updated_at;

    public function __construct()
    {
        $this->setId(null);
        $this->setName(null);
        $this->setCoefficient(null);
        $this->setChance(null);
        $this->setPath(null);
        $this->setCreatedAt(new \DateTimeImmutable("now"));
        $this->setUpdatedAt(new \DateTimeImmutable("now"));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCoefficient(): ?int
    {
        return $this->coefficient;
    }

    public function setCoefficient(?int $coefficient): self
    {
        $this->coefficient = $coefficient;

        return $this;
    }

    public function getChance(): ?int
    {
        return $this->chance;
    }

    public function setChance(?int $chance): self
    {
        $this->chance = $chance;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
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

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
    }
}
