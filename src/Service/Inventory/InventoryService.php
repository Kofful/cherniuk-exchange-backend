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
        if(strpos($oldPath, "/") === false) {
            $sticker->setPath($_ENV["STICKER_PATH"] . $oldPath);
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
        foreach($items as $item) {
            $this->addStickerPath($item->getSticker());
        }
        return $items;
    }
}
