<?php

namespace App\Service\Inventory;

use App\Entity\InventoryItem;
use App\Entity\Sticker;
use App\Entity\User;
use App\Repository\InventoryItemRepository;
use App\Service\Sticker\StickerService;

class InventoryService
{
    private InventoryItemRepository $inventoryItemRepository;
    private StickerService $stickerService;

    public function __construct(
        InventoryItemRepository $inventoryItemRepository,
        StickerService $stickerService
    ) {
        $this->inventoryItemRepository = $inventoryItemRepository;
        $this->stickerService = $stickerService;
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
            $this->stickerService->addPath($item->getSticker());
        }
        return $items;
    }

    public function getOwnItems(int $userId, int $page): array
    {
        $items = $this->inventoryItemRepository->getItemsByUserId($userId, $page);
        foreach ($items as $item) {
            $this->stickerService->addPrice($item->getSticker());
            $this->stickerService->addPath($item->getSticker());
        }
        return $items;
    }

    public function getItemsWithCount(int $userId, int $page, bool $isOwnItems): array
    {
        $itemList = [];
        if ($isOwnItems) {
            $itemList["stickers"] = $this->getOwnItems($userId, $page);
        } else {
            $itemList["stickers"] = $this->getUserItems($userId, $page);
        }
        $itemList["count"] = $this->getUserItemsCount($userId);
        return $itemList;
    }

    public function getUserItemsCount(int $userId): int
    {
        return $this->inventoryItemRepository->getItemsCountByUserId($userId);
    }

    public function sellItem(int $itemId): bool
    {
        $sticker = $this->inventoryItemRepository->find($itemId)->getSticker();
        $price = $this->stickerService->countPrice($sticker);

        return $this->inventoryItemRepository->sellItem($itemId, $price);
    }
}
