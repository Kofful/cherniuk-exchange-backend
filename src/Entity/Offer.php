<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OfferRepository::class)
 * @ORM\Table(name="offers")
 */
class Offer
{
    /**
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
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(
     *     message="offer.payment.creator.required"
     * )
     * @Assert\PositiveOrZero(
     *     message="offer.payment.negative"
     * )
     */
    private ?int $creator_payment;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(
     *     message="offer.payment.target.required"
     * )
     * @Assert\PositiveOrZero(
     *     message="offer.payment.negative"
     * )
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id")
     */
    private ?User $creator;

    /**
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

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate(): void
    {
        $this->setUpdatedAt(new \DateTimeImmutable('now'));
    }
}
