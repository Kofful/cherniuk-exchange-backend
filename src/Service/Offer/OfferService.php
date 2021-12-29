<?php

namespace App\Service\Offer;

use App\Entity\Offer;
use App\Entity\OfferItem;
use App\Entity\Sticker;
use App\Entity\User;
use App\Repository\InventoryItemRepository;
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
    private InventoryItemRepository $inventoryItemRepository;
    private StickerRepository $stickerRepository;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    public function __construct(
        OfferRepository $offerRepository,
        OfferItemRepository $offerItemRepository,
        OfferStatusRepository $offerStatusRepository,
        StickerService $stickerService,
        InventoryItemRepository $inventoryItemRepository,
        StickerRepository $stickerRepository,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository
    ) {
        $this->offerRepository = $offerRepository;
        $this->offerItemRepository = $offerItemRepository;
        $this->offerStatusRepository = $offerStatusRepository;
        $this->inventoryItemRepository = $inventoryItemRepository;
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

    public function checkAcceptPermissions(User $user, int $offerId): array
    {
        $errors = [];
        $offer = $this->offerRepository->find($offerId);
        if (is_null($offer)) {
            $errors[] = "offer.not.found";
        }
        if (isset($offer)) {
            $isUserACreator = $offer->getCreatorId() == $user->getId();
            $isUserATarget = $offer->getTargetId() == null || $offer->getTargetId() == $user->getId();
            $isOfferOpen = $offer->getStatusId() == Offer::STATUS_OPEN_ID;
            if ($isUserACreator || !$isUserATarget || !$isOfferOpen) {
                $errors[] = "offer.accepting.forbidden";
            }
        }
        return $errors;
    }

    private function setStatus(Offer $offer, int $statusId): void
    {
        $status = $this->offerStatusRepository->find($statusId);
        $offer->setStatus($status);
    }

    private function checkParticipantsWallets(User $user, Offer $offer): bool
    {
        $creator = $offer->getCreator();
        $areWalletsEnough = $user->getWallet() >= $offer->getTargetPayment()
            && $creator->getWallet() >= $offer->getCreatorPayment();
        return $areWalletsEnough;
    }

    private function getParticipantsItems(User $user, Offer $offer): array
    {
        $targetItems = $this->inventoryItemRepository->findBy(["owner_id" => $user->getId()]);
        $creatorItems =  $this->inventoryItemRepository->findBy(["owner_id" => $offer->getCreatorId()]);
        $offerItems = $offer->getItems();
        $participantsItems = [];

        //add sticker arrays to search by sticker
        //because targetItems is InventoryItem array and offer items are OfferItem instances
        $targetStickers = array_map(
            function ($item) {
                return $item->getSticker();
            },
            $targetItems
        );
        $creatorStickers = array_map(
            function ($item) {
                return $item->getSticker();
            },
            $creatorItems
        );

        foreach ($offerItems as $item) {
            $isFound = false;
            //search in target items
            if ($item->getIsAccept()) {
                $keyToDelete = array_search($item->getSticker(), $targetStickers);
                if ($keyToDelete !== false) {
                    $isFound = true;
                    //remove from target stickers in case offer has many similar stickers
                    unset($targetStickers[$keyToDelete]);
                    //add to final list that contains items to remove from inventories
                    $participantsItems[] = $targetItems[$keyToDelete];
                }
            } else {
                $keyToDelete = array_search($item->getSticker(), $creatorStickers);
                if ($keyToDelete !== false) {
                    $isFound = true;
                    //remove from target stickers in case offer has many similar stickers
                    unset($creatorStickers[$keyToDelete]);
                    //add to final list that contains items to remove from inventories
                    $participantsItems[] = $creatorItems[$keyToDelete];
                }
            }

            if (!$isFound) {
                return [];
            }
        }

        return $participantsItems;
    }

    public function acceptOffer(User $user, int $offerId): array
    {
        $errors = [];

        $offer = $this->offerRepository->find($offerId);

        $this->setStatus($offer, Offer::STATUS_PENDING_ID);
        $this->entityManager->flush();

        if (!$this->checkParticipantsWallets($user, $offer)) {
            $errors[] = "offer.participant.low.wallet";
        }

        $participantsItems = $this->getParticipantsItems($user, $offer);
        //count 0 means that participants don't have all required stickers
        if (count($participantsItems) == 0) {
            $errors[] = "offer.participant.items.not.enough";
        }

        if (count($errors) > 0) {
            $this->setStatus($offer, Offer::STATUS_OPEN_ID);
        } else {
            $creator = $offer->getCreator();
            $creator->setWallet($creator->getWallet() - $offer->getCreatorPayment());
            $user->setWallet($user->getWallet() - $offer->getTargetPayment());
            foreach ($participantsItems as $item) {
                if ($item->getOwner()->getId() == $user->getId()) {
                    $item->setOwner($creator);
                } else {
                    $item->setOwner($user);
                }
            }
            $offer->setTarget($user);

            $this->setStatus($offer, Offer::STATUS_CLOSED_ID);
        }
        $this->entityManager->flush();

        return $errors;
    }

    public function checkRemovePermissions(User $user, int $offerId): array
    {
        $errors = [];
        $offer = $this->offerRepository->find($offerId);
        if (is_null($offer)) {
            $errors[] = "offer.not.found";
        }
        if (!is_null($offer) && $user->getId() != $offer->getCreatorId()) {
            $errors[] = "offer.deleting.forbidden";
        }
        return $errors;
    }

    public function removeOffer(int $offerId)
    {
        $offer = $this->offerRepository->find($offerId);
        $this->entityManager->remove($offer);
        $this->entityManager->flush();
    }
}
