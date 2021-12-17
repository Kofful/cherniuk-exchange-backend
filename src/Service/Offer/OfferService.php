<?php

namespace App\Service\Offer;

use App\Entity\Offer;
use App\Entity\OfferItem;
use App\Repository\OfferItemRepository;
use App\Repository\OfferRepository;
use App\Repository\OfferStatusRepository;
use App\Repository\StickerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class OfferService
{
    private OfferRepository $offerRepository;
    private OfferItemRepository $offerItemRepository;
    private OfferStatusRepository $offerStatusRepository;
    private StickerRepository $stickerRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        OfferRepository $offerRepository,
        OfferItemRepository $offerItemRepository,
        OfferStatusRepository $offerStatusRepository,
        StickerRepository $stickerRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->offerRepository = $offerRepository;
        $this->offerItemRepository = $offerItemRepository;
        $this->offerStatusRepository = $offerStatusRepository;
        $this->stickerRepository = $stickerRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    private function addItemsToOffer(Offer $offer, array $stickerIds, bool $isAccept): void
    {
        foreach ($stickerIds as $stickerId) {
            $sticker = $this->stickerRepository->find($stickerId);
            $offerItem = new OfferItem();
            $offerItem->setOffer($offer);
            $offerItem->setSticker($sticker);
            $offerItem->setIsAccept($isAccept);
            $this->entityManager->persist($offerItem);
        }
    }

    public function createOffer(Offer $offer): array
    {
        $errors = [];
        if ($offer->getTargetId() !== 0) {
            $target = $this->userRepository->find($offer->getTargetId());
            if (is_null($target)) {
                $errors[] = "offer.target.not.found";
            } else {
                $offer->setTarget($target);
            }
        }

        if (count($errors) === 0) {
            $status = $this->offerStatusRepository->find(Offer::STATUS_OPEN_ID);
            $offer->setStatus($status);
            $this->entityManager->persist($offer);

            $this->entityManager->flush();

            $this->addItemsToOffer($offer, $offer->getGive(), false);
            $this->addItemsToOffer($offer, $offer->getAccept(), true);

            $this->entityManager->flush();
        }

        return $errors;
    }
}
