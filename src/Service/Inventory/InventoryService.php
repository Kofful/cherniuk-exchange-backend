<?php

namespace App\Service\Inventory;

use App\Entity\InventoryItem;
use App\Entity\Sticker;
use App\Entity\User;
use App\Repository\InventoryItemRepository;

class InventoryService
{
    private InventoryItemRepository $inventoryItemRepository;

    public function __construct(InventoryItemRepository $inventoryItemRepository)
    {
        $this->inventoryItemRepository = $inventoryItemRepository;
    }

    public function addStickerPath(Sticker $sticker): void
    {
        $oldPath = $sticker->getPath();

        //check if we haven't changed sticker path already
        //(this can happen because we change sticker fields, but there may be many items with same stickers)
        if (strpos($oldPath, "/") === false) {
            $sticker->setPath($_ENV["STICKER_PATH"] . $oldPath);
        }
    }

    public function getStickerPrice(Sticker $sticker): int
    {
        return floor($sticker->getCoefficient() / 10) + 1;
    }

    public function addStickerPrice(Sticker $sticker): void
    {
        if (!$sticker->getPrice()) {
            // price is counted by formula
            // coefficient / 10 + 1,
            // so it can be a number from 1 to 10_000 (10% of max coefficient)
            // to be a low number to sell
            $price = $this->getStickerPrice($sticker);
            $sticker->setPrice($price);
        }
    }

    public function addItem(User $user, Sticker $sticker): void
    {
        $newItem = new InventoryItem();
        $newItem->setOwner($user);
        $newItem->setSticker($sticker);

        $this->inventoryItemRepository->addItem($newItem);
    }

    public function getUserItems(int $userId, int $page): array
    {
        $items = $this->inventoryItemRepository->getItemsByUserId($userId, $page);
        foreach ($items as $item) {
            $this->addStickerPath($item->getSticker());
        }
        return $items;
    }

    public function getOwnItems(int $userId, int $page): array
    {
        $items = $this->inventoryItemRepository->getItemsByUserId($userId, $page);
        foreach ($items as $item) {
            $this->addStickerPrice($item->getSticker());
            $this->addStickerPath($item->getSticker());
        }
        return $items;
    }

    public function getUserItemsCount(int $userId): int
    {
        return $this->inventoryItemRepository->getItemsCountByUserId($userId);
    }

    public function sellItem(int $itemId): bool
    {
        $sticker = $this->inventoryItemRepository->find($itemId)->getSticker();
        $price = $this->getStickerPrice($sticker);

        return $this->inventoryItemRepository->sellItem($itemId, $price);
    }
}
