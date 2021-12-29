<?php

namespace App\Service\Offer;

use App\Entity\Offer;
use App\Entity\OfferItem;
use App\Entity\Sticker;
use App\Repository\OfferItemRepository;
use App\Repository\OfferRepository;
use App\Repository\OfferStatusRepository;
use App\Repository\StickerRepository;
use App\Repository\UserRepository;
use App\Service\Inventory\InventoryService;
use App\Service\Sticker\StickerService;
use Doctrine\ORM\EntityManagerInterface;

class OfferService
{
    private OfferRepository $offerRepository;
    private OfferItemRepository $offerItemRepository;
    private OfferStatusRepository $offerStatusRepository;
    private StickerService $stickerService;
    private StickerRepository $stickerRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        OfferRepository $offerRepository,
        OfferItemRepository $offerItemRepository,
        OfferStatusRepository $offerStatusRepository,
        StickerService $stickerService,
        StickerRepository $stickerRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->offerRepository = $offerRepository;
        $this->offerItemRepository = $offerItemRepository;
        $this->offerStatusRepository = $offerStatusRepository;
        $this->stickerService = $stickerService;
        $this->stickerRepository = $stickerRepository;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    private function splitOfferItems(Offer $offer): void
    {
        $giveItems = [];
        $acceptItems = [];
        foreach ($offer->getItems() as $item) {
            $sticker = $item->getSticker();
            $this->stickerService->addPath($sticker);
            if ($item->getIsAccept()) {
                $acceptItems[] = $sticker;
            } else {
                $giveItems[] = $sticker;
            }
        }
        $offer->setGiveItems($giveItems);
        $offer->setAcceptItems($acceptItems);
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

    public function getOffers(int $page): array
    {
        $offset = ($page - 1) * OfferRepository::OFFER_COUNT_PER_PAGE;
        $offers = $this->offerRepository->findBy(
            [
                "status_id" => Offer::STATUS_OPEN_ID,
                "target_id" => null
            ],
            ["created_at" => "ASC"],
            OfferRepository::OFFER_COUNT_PER_PAGE,
            $offset
        );
        foreach ($offers as $offer) {
            $this->splitOfferItems($offer);
        }

        return $offers;
    }

    public function getCount(): int
    {
        return $this->offerRepository->count(
            [
                "status_id" => Offer::STATUS_OPEN_ID,
                "target_id" => null
            ]
        );
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
