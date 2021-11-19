<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Inventory\InventoryService;
use App\Service\Sticker\StickerService;
use App\Service\User\UserService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    public function index(UserService $userService): Response
    {
        $user = $userService->getUser($this->getUser());
        return $this->json([
            "user" => $user
        ]);
    }

    public function dropSticker(
        StickerService $stickerService,
        UserService $userService,
        InventoryService $inventoryService,
        TranslatorInterface $translator
    ): Response
    {
        $response = [];
        $status = 200;

        $user = $this->getUser();

        $sticker = $stickerService->dropSticker($user);

        if (isset($sticker)) {
            $response = $sticker;
            $inventoryService->addItem($user, $sticker);
            $userService->updateRewardedAt($user);
        } else {
            $response = [$translator->trans("sticker.cannot.receive", [], "responses")];
            $status = 403;
        }

        return $this->json($response, $status);
    }
}
