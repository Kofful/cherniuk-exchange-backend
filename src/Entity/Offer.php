<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 * @ORM\Table(name="offers")
 */
class Offer
{
    public const STATUS_OPEN_ID = 1;
    public const STATUS_PENDING_ID = 2;
    public const STATUS_CLOSED_ID = 3;

    /**
     * @Groups("allOffers")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $creator_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(
     *     message="offer.target.id.required"
     * )
     * @Assert\PositiveOrZero(
     *     message="offer.target.id.negative"
     * )
     */
    private ?int $target_id;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $status_id;

    /**
     * @Groups("allOffers")
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(
     *     message="offer.payment.creator.required"
     * )
     * @Assert\PositiveOrZero(
     *     message="offer.payment.negative"
     * )
     * @SerializedName("creatorPayment")
     */
    private ?int $creator_payment;

    /**
     * @Groups("allOffers")
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(
     *     message="offer.payment.target.required"
     * )
     * @Assert\PositiveOrZero(
     *     message="offer.payment.negative"
     * )
     * @SerializedName("targetPayment")
     */
    private ?int $target_payment;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private \DateTimeImmutable $created_at;

    /**
     * @ORM\Column(type="datetime_immutable", options={"default" : "CURRENT_TIMESTAMP"})
     */
    private \DateTimeImmutable $updated_at;

    /**
     * @Groups("allOffers")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     */
    private ?User $creator;

    /**
     * @Groups("allOffers")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="target_id", referencedColumnName="id")
     */
    private ?User $target;

    /**
     * @ORM\ManyToOne(targetEntity="OfferStatus")
     * @ORM\JoinColumn(name="status_id", referencedColumnName="id")
     */
    private ?OfferStatus $status;

    /**
     * @ORM\OneToMany(targetEntity="OfferItem", mappedBy="offer")
     */
    private ?Collection $items;

    /**
     * @Assert\NotBlank(
     *     message="offer.give.required"
     * )
     * @var int[]|null
     */
    private ?array $give;

    /**
     * @Assert\NotBlank(
     *     message="offer.accept.required"
     * )
     * @var int[]|null
     */
    private ?array $accept;

    /**
     * @Groups("allOffers")
     * @var Sticker[]|null
     */
    private ?array $giveItems;

    /**
     * @Groups("allOffers")
     * @var Sticker[]|null
     */
    private ?array $acceptItems;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatorId(): ?int
    {
        return $this->creator_id;
    }

    public function setCreatorId(int $creator_id): self
    {
        $this->creator_id = $creator_id;

        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->target_id;
    }

    public function setTargetId(int $target_id): self
    {
        $this->target_id = $target_id;

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

    public function getCreatorPayment(): ?int
    {
        return $this->creator_payment;
    }

    public function setCreatorPayment(int $creator_payment): self
    {
        $this->creator_payment = $creator_payment;

        return $this;
    }

    public function getTargetPayment(): ?int
    {
        return $this->target_payment;
    }

    public function setTargetPayment(int $target_payment): self
    {
        $this->target_payment = $target_payment;

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

    public function getCreator(): ?User
    {
        return $this->creator;
    }

    public function setCreator($creator): void
    {
        $this->creator = $creator;
    }

    public function getTarget(): ?User
    {
        return $this->target;
    }

    public function setTarget($target): void
    {
        $this->target = $target;
    }

    public function getStatus(): ?OfferStatus
    {
        return $this->status;
    }

    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function getGive(): ?array
    {
        return $this->give;
    }

    public function setGive(?array $give): void
    {
        $this->give = $give;
    }

    public function getAccept(): ?array
    {
        return $this->accept;
    }

    public function setAccept(?array $accept): void
    {
        $this->accept = $accept;
    }

    public function getItems(): ?Collection
    {
        return $this->items;
    }

    public function setItems(?Collection $items): void
    {
        $this->items = $items;
    }

    /**
     * @return Collection|null
     */
    public function getItems(): ?Collection
    {
        return $this->items;
    }

    /**
     * @param Collection|null $items
     */
    public function setItems(?Collection $items): void
    {
        $this->items = $items;
    }

    /**
     * @return Sticker[]|null
     */
    public function getGiveItems(): ?array
    {
        return $this->giveItems;
    }

    /**
     * @param Sticker[]|null $giveItems
     */
    public function setGiveItems(?array $giveItems): void
    {
        $this->giveItems = $giveItems;
    }

    /**
     * @return Sticker[]|null
     */
    public function getAcceptItems(): ?array
    {
        return $this->acceptItems;
    }

    /**
     * @param Sticker[]|null $acceptItems
     */
    public function setAcceptItems(?array $acceptItems): void
    {
        $this->acceptItems = $acceptItems;
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
    }

    public function __construct()
    {
        $this->setCreatedAt(new \DateTimeImmutable("now"));
        $this->setUpdatedAt(new \DateTimeImmutable("now"));
    }
}
