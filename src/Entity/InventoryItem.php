<?php

namespace App\Entity;

use App\Repository\InventoryItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InventoryItemRepository::class)
 * @ORM\Table(name="inventory_items")
 */
class InventoryItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sticker_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $owner_id;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @ORM\ManyToOne(targetEntity="Sticker")
     * @ORM\JoinColumn(name="sticker_id", referencedColumnName="id")
     */
    private $sticker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStickerId(): ?int
    {
        return $this->sticker_id;
    }

    public function setStickerId(int $sticker_id): self
    {
        $this->sticker_id = $sticker_id;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->owner_id;
    }

    public function setOwnerId(int $owner_id): self
    {
        $this->owner_id = $owner_id;

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

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
    }
}
