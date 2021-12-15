<?php

namespace App\Controller;

use App\Repository\InventoryItemRepository;
use App\Repository\UserRepository;
use App\Service\Inventory\InventoryService;
use App\Service\StatusCode;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventoryItemController extends AbstractController
{
    public function getUserItems(
        InventoryService $inventoryService,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        SerializerInterface $serializer,
        Request $request
    ): Response {
        $response = [];
        $status = StatusCode::STATUS_OK;

        $page = $request->query->get("page") ?? 1;

        $userId = $request->attributes->get("id");
        $user = $userRepository->find($userId);
        $isInOwnProfile = false;

        if (isset($user)) {
            $isInOwnProfile = $this->getUser() && $this->getUser()->getId() == $user->getId();
            if ($isInOwnProfile) {
                $response["stickers"] = $inventoryService->getOwnItems($user->getId(), $page);
            } else {
                $response["stickers"] = $inventoryService->getUserItems($user->getId(), $page);
            }
            $response["count"] = $inventoryService->getUserItemsCount($user->getId());
        } else {
            $status = StatusCode::STATUS_BAD_REQUEST;
            $response = [$translator->trans("user.not.found", [], "responses")];
        }

        $groups = [
            "userItems",
            $isInOwnProfile ? "ownItems" : null
        ];

        return $this->json($response, $status, [], ["groups" => $groups]);
    }

    public function sellItem(
        InventoryService $inventoryService,
        InventoryItemRepository $inventoryItemRepository,
        TranslatorInterface $translator,
        Request $request
    ): Response {
        $response = [];
        $status = StatusCode::STATUS_OK;
        $itemId = $request->attributes->get("id");
        $item = $inventoryItemRepository->find($itemId);

        if (!$item) {
            $response = [$translator->trans("item.not.found", [], "responses")];
            $status = StatusCode::STATUS_BAD_REQUEST;
            return $this->json($response, $status);
        }

        if ($this->getUser()->getId() != $item->getOwnerId()) {
            $response = [$translator->trans("item.cannot.sell", [], "responses")];
            $status = StatusCode::STATUS_ACCESS_DENIED;
            return $this->json($response, $status);
        }

        if (!$inventoryService->sellItem($itemId)) {
            $status = StatusCode::STATUS_ACCESS_DENIED;
        }

        return $this->json($response, $status);
    }
}
