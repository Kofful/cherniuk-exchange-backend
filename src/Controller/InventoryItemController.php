<?php

namespace App\Controller;

use App\Repository\InventoryItemRepository;
use App\Repository\UserRepository;
use App\Service\Inventory\InventoryService;
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
        Request $request
    ): Response {
        $status = Response::HTTP_OK;

        $page = $request->query->get("page") ?? 1;

        $userId = $request->attributes->get("id");
        $user = $userRepository->find($userId);
        $isInOwnProfile = false;

        if (isset($user)) {
            $isInOwnProfile = $this->getUser() && $this->getUser()->getId() == $user->getId();
            $response = $inventoryService->getItemsWithCount($user->getId(), $page, $isInOwnProfile);
        } else {
            $status = Response::HTTP_BAD_REQUEST;
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
        $status = Response::HTTP_OK;
        $itemId = $request->attributes->get("id");
        $item = $inventoryItemRepository->find($itemId);

        if (!$item) {
            $response = [$translator->trans("item.not.found", [], "responses")];
            $status = Response::HTTP_BAD_REQUEST;
            return $this->json($response, $status);
        }

        if ($this->getUser()->getId() != $item->getOwnerId()) {
            $response = [$translator->trans("item.cannot.sell", [], "responses")];
            $status = Response::HTTP_FORBIDDEN;
            return $this->json($response, $status);
        }

        if (!$inventoryService->sellItem($itemId)) {
            $status = Response::HTTP_FORBIDDEN;
        }

        return $this->json($response, $status);
    }
}
