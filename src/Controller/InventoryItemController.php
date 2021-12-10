<?php

namespace App\Controller;

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
                $response = $inventoryService->getOwnItems($user->getId(), $page);
            } else {
                $response = $inventoryService->getUserItems($user->getId(), $page);
            }
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
}
