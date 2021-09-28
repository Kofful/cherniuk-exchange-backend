<?php

namespace App\Entity;

use App\Repository\OfferItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OfferItemRepository::class)
 * @ORM\Table(name="offer_items")
 */
class OfferItem
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
    private $offer_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sticker_id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $is_accept;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="Offer")
     * @ORM\JoinColumn(name="offer_id", referencedColumnName="id")
     */
    private $offer;

    /**
     * @ORM\ManyToOne(targetEntity="Sticker")
     * @ORM\JoinColumn(name="sticker_id", referencedColumnName="id")
     */
    private $sticker;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOfferId(): ?int
    {
        return $this->offer_id;
    }

    public function setOfferId(int $offer_id): self
    {
        $this->offer_id = $offer_id;

        return $this;
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

    public function getIsAccept(): ?bool
    {
        return $this->is_accept;
    }

    public function setIsAccept(bool $is_accept): self
    {
        $this->is_accept = $is_accept;

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

    public function getOffer()
    {
        return $this->offer;
    }

    public function setOffer($offer): void
    {
        $this->offer = $offer;
    }

    public function getSticker()
    {
        return $this->sticker;
    }

    public function setSticker($sticker): void
    {
        $this->sticker = $sticker;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
    }
}
