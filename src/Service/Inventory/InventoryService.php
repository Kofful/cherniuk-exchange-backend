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

    public function addItem(User $user, Sticker $sticker): void
    {
        $newItem = new InventoryItem();
        $newItem->setOwner($user);
        $newItem->setSticker($sticker);

        $this->inventoryItemRepository->addItem($newItem);
    }

    public function getUserItems(int $userId, int $page): array
    {
        return $this->inventoryItemRepository->getItemsByUserId($userId, $page);
    }
}
